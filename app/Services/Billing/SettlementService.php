<?php

namespace App\Services\Billing;

use App\Enums\InvoiceStatus;
use App\Models\CashPayment;
use App\Models\CashPoint;
use App\Models\CashShift;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\Service;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Settlement — "the patient pays for these services at this till".
 *
 * The counter flow is: pick the patient, see what is still owed, tick the
 * services, take the money, print a receipt. This service owns that whole
 * transaction so no screen has to re-implement it:
 *
 *  - a payment always settles named charges (invoice lines), never a free amount;
 *  - a charge is marked paid only when it is fully covered;
 *  - a re-sent identical request (same `client_uuid`) returns the first payment
 *    instead of charging the patient twice;
 *  - the payment is attached to the till (`cash_point`) it was taken at.
 */
class SettlementService
{
    /**
     * Invoice line types owned by the pharmacy counter.
     *
     * @var array<int, string>
     */
    public const PHARMACY_ITEM_TYPES = ['pharmacy', 'medication'];

    public function __construct(private readonly BillingService $billing) {}

    /**
     * What the patient still owes, grouped by module, plus the priced catalogue
     * the cashier may bill on the spot.
     *
     * @return array{charges: array<int, array<string, mixed>>, services: array<int, array<string, mixed>>}
     */
    public function counterPayload(Patient $patient, ?CashPoint $point = null): array
    {
        return [
            'charges' => $this->outstandingCharges($patient, $point),
            'services' => $this->serviceCatalogue($point),
        ];
    }

    /**
     * Unsettled invoice lines for a patient, newest invoice first.
     *
     * @return array<int, array<string, mixed>>
     */
    public function outstandingCharges(Patient $patient, ?CashPoint $point = null): array
    {
        $items = InvoiceItem::query()
            ->unsettled()
            ->whereHas('invoice', function ($query) use ($patient): void {
                $query->where('patient_id', $patient->id)
                    ->whereIn('status', $this->collectibleStatuses());
            })
            ->when($point !== null, function ($query) use ($point): void {
                $allowed = $point->collectableItemTypes();

                if ($allowed === null) {
                    $query->whereNotIn('item_type', self::PHARMACY_ITEM_TYPES);

                    return;
                }

                $query->whereIn('item_type', $allowed);
            })
            ->with('invoice:id,invoice_number,status')
            ->orderBy('invoice_id')
            ->orderBy('id')
            ->get();

        return $items
            ->map(fn (InvoiceItem $item): array => [
                'id' => $item->id,
                'invoice_id' => $item->invoice_id,
                'invoice_number' => $item->invoice?->invoice_number,
                'module' => $this->moduleFor($item->item_type),
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'total_price' => (float) $item->total_price,
            ])
            ->values()
            ->all();
    }

    /**
     * Priced catalogue the cashier can bill without a doctor's order
     * (walk-ins, procedures booked at the desk).
     *
     * @return array<int, array<string, mixed>>
     */
    public function serviceCatalogue(?CashPoint $point = null): array
    {
        $categories = match ($point?->module) {
            'laboratory' => ['lab'],
            'radiology' => ['radiology', 'imaging'],
            'pharmacy' => ['medication'],
            'consultation' => ['consultation'],
            'main' => ['consultation', 'procedure', 'nursing', 'emergency', 'lab', 'radiology', 'imaging', 'other'],
            default => null,
        };

        return Service::query()
            ->where('is_active', true)
            // The main caisse never sells drugs: the pharmacy counter does.
            ->when($categories === null && $point !== null, function ($query): void {
                $query->whereNotIn('category', ['medication']);
            })
            ->when($categories !== null, fn ($query) => $query->whereIn('category', $categories))
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'category', 'default_price', 'requires_upfront_payment'])
            ->map(fn (Service $service): array => [
                'id' => $service->id,
                'name' => $service->name,
                'code' => $service->code,
                'category' => $service->category?->value,
                'price' => (float) $service->default_price,
                'requires_upfront_payment' => (bool) $service->requires_upfront_payment,
            ])
            ->values()
            ->all();
    }

    /**
     * Add catalogue services to the patient's running bill and return the new
     * invoice line ids so the counter can settle them in the same step.
     *
     * @param  array<int, int>  $serviceIds
     * @return array<int, int>
     */
    public function billServices(Patient $patient, array $serviceIds, ?CashPoint $point = null): array
    {
        $services = Service::query()
            ->whereIn('id', $serviceIds)
            ->where('is_active', true)
            ->get();

        if ($services->count() !== count(array_unique($serviceIds))) {
            throw ValidationException::withMessages([
                'service_ids' => 'One of the selected services is no longer available.',
            ]);
        }

        if ($point !== null) {
            $notSellable = $services->reject(
                fn (Service $service): bool => $point->canCollect($service->category?->value ?? 'other'),
            );

            if ($notSellable->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'service_ids' => 'This till cannot sell those services. Use the right counter.',
                ]);
            }
        }

        return DB::transaction(function () use ($patient, $services): array {
            $invoice = $this->billing->findOrCreateDraftInvoice($patient->id);
            $itemIds = [];

            foreach ($services as $service) {
                $itemType = $service->category?->value ?? 'service';

                $item = $this->billing->addBillableItem(
                    $invoice,
                    $itemType,
                    $service->id,
                    $service->name,
                    (float) $service->default_price,
                );

                // `addBillableItem()` refuses to bill the same service twice on
                // one invoice. For the counter that means "this is already on
                // the bill": settle that line if it is still unpaid, and refuse
                // only when it has already been paid.
                if ($item === null) {
                    $existing = $invoice->items()
                        ->where('item_type', $itemType)
                        ->where('item_id', $service->id)
                        ->latest('id')
                        ->first();

                    if ($existing === null) {
                        continue;
                    }

                    if ($existing->isSettled()) {
                        throw ValidationException::withMessages([
                            'service_ids' => "{$service->name} has already been paid for on this bill.",
                        ]);
                    }

                    $itemIds[] = $existing->id;

                    continue;
                }

                $itemIds[] = $item->id;
            }

            return $itemIds;
        });
    }

    /**
     * Take the money for the selected charges.
     *
     * @param  array<int, int>  $itemIds
     *
     * @throws ValidationException when nothing is selected, the lines do not
     *                             belong to the patient, or the money does not
     *                             cover the selected services.
     */
    public function settle(
        Patient $patient,
        array $itemIds,
        CashShift $shift,
        string $paymentMethod,
        float $amountTendered,
        ?string $clientUuid = null,
    ): CashPayment {
        $itemIds = array_values(array_unique(array_map('intval', $itemIds)));

        if ($itemIds === []) {
            throw ValidationException::withMessages([
                'item_ids' => 'Select at least one service to pay for.',
            ]);
        }

        // Re-sent request (double click, retry after a dropped connection):
        // return the payment that already happened instead of charging again.
        if ($clientUuid !== null) {
            $existing = CashPayment::where('client_uuid', $clientUuid)->first();

            if ($existing !== null) {
                return $existing;
            }
        }

        return DB::transaction(function () use ($patient, $itemIds, $shift, $paymentMethod, $amountTendered, $clientUuid): CashPayment {
            $items = InvoiceItem::query()
                ->unsettled()
                ->whereIn('id', $itemIds)
                ->whereHas('invoice', function ($query) use ($patient): void {
                    $query->where('patient_id', $patient->id)
                        ->whereIn('status', $this->collectibleStatuses());
                })
                ->with('invoice')
                ->lockForUpdate()
                ->get();

            if ($items->count() !== count($itemIds)) {
                throw ValidationException::withMessages([
                    'item_ids' => 'Some of those services are already paid or do not belong to this patient.',
                ]);
            }

            $point = $shift->cashPoint;

            if ($point !== null) {
                $forbidden = $items->reject(
                    fn (InvoiceItem $item): bool => $point->canCollect($item->item_type),
                );

                if ($forbidden->isNotEmpty()) {
                    throw ValidationException::withMessages([
                        'item_ids' => 'This till cannot collect those services. Use the right counter.',
                    ]);
                }
            }

            $total = round($items->sum(fn (InvoiceItem $item): float => (float) $item->total_price), 2);

            if ($total <= 0) {
                throw ValidationException::withMessages([
                    'item_ids' => 'Those services have no price to collect.',
                ]);
            }

            if ($amountTendered < $total) {
                throw ValidationException::withMessages([
                    'amount_tendered' => sprintf(
                        'The amount received (%s) does not cover the selected services (%s).',
                        $this->formatMoney($amountTendered),
                        $this->formatMoney($total),
                    ),
                ]);
            }

            $settledAt = now();

            InvoiceItem::whereIn('id', $items->pluck('id'))->update(['settled_at' => $settledAt]);

            $invoice = $items->first()->invoice;

            $payment = CashPayment::create([
                'cash_shift_id' => $shift->id,
                'cash_point_id' => $shift->cash_point_id,
                'invoice_id' => $invoice->id,
                'patient_id' => $patient->id,
                'amount_paid' => $total,
                'payment_method' => $paymentMethod,
                'client_uuid' => $clientUuid,
            ]);

            $this->refreshInvoiceStatus($invoice);

            return $payment;
        });
    }

    /**
     * Change owed back to the patient, given what was handed over.
     */
    public function changeDue(float $amountTendered, float $total): float
    {
        return round(max(0, $amountTendered - $total), 2);
    }

    /**
     * Close or reopen the invoice based on every line being settled.
     */
    private function refreshInvoiceStatus(Invoice $invoice): void
    {
        $invoice->refresh();

        $hasUnsettled = $invoice->items()->unsettled()->exists();
        $cashPaid = (float) $invoice->cashPayments()->sum('amount_paid');
        $amountDue = (float) $invoice->total_amount;

        if ($cashPaid > 0 && (! $hasUnsettled || $cashPaid >= $amountDue)) {
            $this->billing->updateStatus($invoice, InvoiceStatus::PAID);

            return;
        }

        if ($invoice->status === InvoiceStatus::DRAFT) {
            $this->billing->updateStatus($invoice, InvoiceStatus::PENDING);
        }
    }

    /**
     * Invoice states the counter may still collect against.
     *
     * A bill that was already settled (or cancelled) must never resurface at the
     * till: lines on it were paid before per-service settlement existed, so
     * their `settled_at` is null and they would otherwise read as owed.
     *
     * @return array<int, string>
     */
    private function collectibleStatuses(): array
    {
        return [
            InvoiceStatus::DRAFT->value,
            InvoiceStatus::PENDING->value,
            InvoiceStatus::OVERDUE->value,
        ];
    }

    /**
     * Map a billable line to the module that owns the service.
     */
    private function moduleFor(?string $itemType): string
    {
        return match ($itemType) {
            'lab_test', 'laboratory' => 'laboratory',
            'pharmacy' => 'pharmacy',
            'radiology' => 'radiology',
            'consultation', 'appointment' => 'consultation',
            'admission', 'bed' => 'admission',
            'surgery' => 'surgery',
            'emergency' => 'emergency',
            default => 'other',
        };
    }

    private function formatMoney(float $amount): string
    {
        return number_format($amount, 0, '.', ' ').' FCFA';
    }

    /**
     * Charges for a patient as a flat collection, used by tests and reports.
     *
     * @return Collection<int, InvoiceItem>
     */
    public function unsettledItems(Patient $patient): Collection
    {
        return InvoiceItem::query()
            ->unsettled()
            ->whereHas('invoice', function ($query) use ($patient): void {
                $query->where('patient_id', $patient->id)
                    ->whereIn('status', $this->collectibleStatuses());
            })
            ->get();
    }
}
