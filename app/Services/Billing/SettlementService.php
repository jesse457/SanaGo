<?php

namespace App\Services\Billing;

use App\Enums\ClaimStatus;
use App\Enums\InvoiceStatus;
use App\Enums\PatientChargeCategory;
use App\Enums\PaymentStatus;
use App\Models\CashPayment;
use App\Models\CashPoint;
use App\Models\CashShift;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\Payer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Settlement — "the patient pays for these services at this till".
 *
 * The counter flow is: pick the patient, see what is still owed, add what they
 * are paying for — from the drug stock, the laboratory catalogue, the running
 * stay or the price list — take the money, print a receipt. This service owns
 * that whole transaction so no screen has to re-implement it:
 *
 *  - a payment always settles named charges (invoice lines), never a free amount;
 *  - a charge is marked paid only when it is fully covered;
 *  - a re-sent identical request (same `client_uuid`) returns the first payment
 *    instead of charging the patient twice;
 *  - the payment is attached to the till (`cash_point`) it was taken at.
 */
class SettlementService
{
    public function __construct(
        private readonly BillingService $billing,
        private readonly CounterCatalogue $catalogue,
    ) {}

    /**
     * What the desk shows for a patient: the bills themselves, what is still to
     * pay, everything the till may sell (drugs and lab tests included), the
     * open stay if the patient is admitted, and the headings a hand-written
     * charge can be filed under.
     *
     * @return array{bills: array<int, array<string, mixed>>, charges: array<int, array<string, mixed>>, catalogue: array<int, array<string, mixed>>, modules: array<int, array{value: string, label: string}>, admission: array<string, mixed>|null, categories: array<int, array<string, mixed>>}
     */
    public function counterPayload(Patient $patient, ?CashPoint $point = null): array
    {
        return [
            'bills' => $this->bills($patient),
            'charges' => $this->outstandingCharges($patient, $point),
            'catalogue' => $this->catalogue->picks($point),
            'modules' => $this->catalogue->modules($point),
            'admission' => $this->catalogue->ongoingAdmission($patient),
            'categories' => $this->chargeCategories($point),
        ];
    }

    /**
     * Every bill the patient has, with its lines, payments and insurance claim.
     *
     * The counter is the only place a bill is read now, so this carries the
     * whole record — what was charged, what was paid, what is left.
     *
     * @return array<int, array<string, mixed>>
     */
    public function bills(Patient $patient): array
    {
        return Invoice::query()
            ->where('patient_id', $patient->id)
            ->with([
                'items' => fn ($query) => $query->orderBy('id'),
                'cashPayments' => fn ($query) => $query->orderBy('id'),
                'claim.payer:id,name,coverage_percentage',
            ])
            ->orderByDesc('id')
            ->get()
            ->map(function (Invoice $invoice): array {
                $open = in_array($invoice->status->value, $this->collectibleStatuses(), true);

                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'status' => $invoice->status->value,
                    'invoice_date' => $invoice->invoice_date?->format('Y-m-d'),
                    'total_amount' => (float) $invoice->total_amount,
                    'total_paid' => $invoice->total_paid,
                    'remaining_balance' => $invoice->remaining_balance,
                    'is_open' => $open,
                    'claim' => $invoice->claim === null ? null : [
                        'id' => $invoice->claim->id,
                        'status' => $invoice->claim->status->value,
                        'claim_amount' => (float) $invoice->claim->claim_amount,
                        'payer' => $invoice->claim->payer?->name,
                    ],
                    'payments' => $invoice->cashPayments
                        ->map(fn (CashPayment $payment): array => [
                            'id' => $payment->id,
                            'amount' => (float) $payment->amount_paid,
                            'method' => $payment->payment_method,
                            'paid_at' => $payment->created_at?->toIso8601String(),
                        ])
                        ->all(),
                    'items' => $invoice->items
                        ->map(fn (InvoiceItem $item): array => [
                            'id' => $item->id,
                            'description' => $item->description,
                            'item_type' => $item->item_type,
                            'module' => $this->moduleFor($item->item_type),
                            'quantity' => $item->quantity,
                            'unit_price' => (float) $item->unit_price,
                            'total_price' => (float) $item->total_price,
                            'paid_amount' => (float) $item->paid_amount,
                            'payment_status' => $item->payment_status?->value ?? PaymentStatus::Pending->value,
                            'settled' => $item->isSettled(),
                            'manual' => $item->item_id === null,
                            // Only hand-written lines still unpaid may be
                            // corrected here: a doctor-ordered test is changed
                            // in its own module, not at the cash desk.
                            'adjustable' => $open && ! $item->isSettled() && $item->item_id === null,
                        ])
                        ->all(),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * The bills list the Finance → Invoices screen used to own.
     *
     * @param  array{search?: string|null, status?: string|null}  $filters
     * @return LengthAwarePaginator<int, array<string, mixed>>
     */
    public function billList(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $status = (string) ($filters['status'] ?? 'all');

        return Invoice::query()
            ->with([
                'patient:id,first_name,last_name,patient_uid',
                'claim:id,invoice_id,claim_amount,status',
            ])
            ->withCount('items')
            ->withSum('cashPayments as cash_paid', 'amount_paid')
            ->when($status !== '' && $status !== 'all', fn ($query) => $query->where('status', $status))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('patient', fn ($patient) => $patient
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('patient_uid', 'like', "%{$search}%"));
                });
            })
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString()
            ->through(function (Invoice $invoice): array {
                $paid = (float) ($invoice->cash_paid ?? 0)
                    + ($invoice->claim?->status === ClaimStatus::Paid
                        ? (float) $invoice->claim->claim_amount
                        : 0.0);

                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'patient_id' => $invoice->patient_id,
                    'patient_name' => $invoice->patient
                        ? trim($invoice->patient->first_name.' '.$invoice->patient->last_name)
                        : '—',
                    'patient_uid' => $invoice->patient?->patient_uid,
                    'invoice_date' => $invoice->invoice_date?->format('Y-m-d'),
                    'status' => $invoice->status->value,
                    'total_amount' => (float) $invoice->total_amount,
                    'remaining_balance' => max(0, round((float) $invoice->total_amount - $paid, 2)),
                    'items_count' => (int) $invoice->items_count,
                ];
            });
    }

    /**
     * Charge categories this till may write by hand.
     *
     * A pharmacy till only files pharmacy charges; the main desk files
     * everything else. Never a hard-coded list in the UI.
     *
     * @return array<int, array{value: string, module: string}>
     */
    public function chargeCategories(?CashPoint $point = null): array
    {
        $categories = array_filter(
            PatientChargeCategory::cases(),
            fn (PatientChargeCategory $category): bool => $point === null
                || $point->canCollect($category->value),
        );

        return array_values(array_map(
            fn (PatientChargeCategory $category): array => [
                'value' => $category->value,
                'module' => $this->moduleFor($category->value),
            ],
            $categories,
        ));
    }

    /**
     * Write charges by hand onto the patient's running bill.
     *
     * Nothing here comes from the price list: the cashier names the line, says
     * how many and at what price. Each line is stored with `item_id` null, so
     * the same heading may be billed as often as the stay needs it.
     *
     * @param  array<int, array{description: string, category: string, quantity: int|string, unit_price: float|string}>  $lines
     * @return array<int, int> the new invoice line ids
     *
     * @throws ValidationException when this till may not bill that category
     */
    public function addCharges(Patient $patient, array $lines, ?CashPoint $point = null): array
    {
        return DB::transaction(function () use ($patient, $lines, $point): array {
            $invoice = $this->billing->findOrCreateDraftInvoice($patient->id);
            $itemIds = [];

            foreach ($lines as $line) {
                $category = PatientChargeCategory::from((string) $line['category']);

                if ($point !== null && ! $point->canCollect($category->value)) {
                    throw ValidationException::withMessages([
                        'lines' => 'This till cannot bill that kind of charge. Use the right counter.',
                    ]);
                }

                $item = $this->billing->addItem($invoice, [
                    'item_type' => $category->value,
                    'item_id' => null,
                    'description' => (string) $line['description'],
                    'quantity' => max(1, (int) $line['quantity']),
                    'unit_price' => round((float) $line['unit_price'], 2),
                ]);

                $itemIds[] = $item->id;
            }

            return $itemIds;
        });
    }

    /**
     * Correct a hand-written charge before it is paid.
     *
     * @param  array{description?: string, quantity?: int|string, unit_price?: float|string}  $changes
     *
     * @throws ValidationException when the line is paid, closed, or was not
     *                             written by hand
     */
    public function updateCharge(InvoiceItem $item, array $changes): InvoiceItem
    {
        return DB::transaction(function () use ($item, $changes): InvoiceItem {
            $this->assertAdjustable($item);

            $item->fill(array_intersect_key(
                $changes,
                array_flip(['description', 'quantity', 'unit_price']),
            ));
            $item->total_price = (int) $item->quantity * (float) $item->unit_price;
            $item->save();

            $this->billing->recalculateTotals($item->invoice);

            return $item->refresh();
        });
    }

    /**
     * Remove a hand-written charge that should never have been on the bill.
     *
     * @throws ValidationException when the line is paid, closed, or was not
     *                             written by hand
     */
    public function voidCharge(InvoiceItem $item): void
    {
        DB::transaction(function () use ($item): void {
            $this->assertAdjustable($item);

            $this->billing->removeItem($item->invoice, $item);
        });
    }

    /**
     * A line may only be corrected while it is unpaid, on a bill the desk still
     * collects against, and only if the desk wrote it.
     *
     * @throws ValidationException
     */
    private function assertAdjustable(InvoiceItem $item): void
    {
        if ($item->item_id !== null) {
            throw ValidationException::withMessages([
                'lines' => 'This charge came from another module. Correct it there, not at the cash desk.',
            ]);
        }

        if ($item->isSettled()
            || ($item->payment_status !== null && $item->payment_status !== PaymentStatus::Pending)) {
            throw ValidationException::withMessages([
                'lines' => 'This charge has already been paid and can no longer be changed.',
            ]);
        }

        $invoice = $item->invoice;

        if ($invoice === null || ! in_array($invoice->status->value, $this->collectibleStatuses(), true)) {
            throw ValidationException::withMessages([
                'lines' => 'This bill is closed, so its lines can no longer be changed.',
            ]);
        }
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
                    // The main desk collects everything: leaving drug charges
                    // out would strand them at a till the patient cannot reach.
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
                'paid_amount' => (float) $item->paid_amount,
                'amount_due' => round((float) $item->total_price - (float) $item->paid_amount, 2),
                'payment_status' => $item->payment_status?->value ?? PaymentStatus::Pending->value,
            ])
            ->values()
            ->all();
    }

    /**
     * Put the picked catalogue items on the patient's running bill and return
     * the invoice line ids so the counter can settle them in the same step.
     *
     * A pick names the record behind the line (`source` + `id`) instead of a
     * price, so the counter can sell a drug from the pharmacy stock, a test
     * from the laboratory catalogue, a bed day from the open stay or a service
     * from the price list without ever typing an amount.
     *
     * The quantity is the number of units the patient really owes *in total*
     * (three nights of a stay, four boxes of a drug): when the line is already
     * on the bill unpaid, it is topped up instead of being billed twice.
     *
     * @param  array<int, array{source: string, id: int|string, quantity?: int|string|null}>  $picks
     * @return array<int, int>
     *
     * @throws ValidationException when a pick is gone, belongs to another
     *                             patient, or this till may not sell it
     */
    public function billPicks(Patient $patient, array $picks, ?CashPoint $point = null): array
    {
        return DB::transaction(function () use ($patient, $picks, $point): array {
            $invoice = $this->billing->findOrCreateDraftInvoice($patient->id);
            $itemIds = [];

            foreach ($picks as $pick) {
                $row = $this->catalogue->resolve(
                    (string) ($pick['source'] ?? CounterCatalogue::SOURCE_SERVICE),
                    (int) $pick['id'],
                    $patient,
                );

                if ($row === null) {
                    throw ValidationException::withMessages([
                        'picks' => 'One of the selected items is no longer available.',
                    ]);
                }

                if (! $this->catalogue->sellable($point, $row)) {
                    throw ValidationException::withMessages([
                        'picks' => 'This till cannot sell those items. Use the right counter.',
                    ]);
                }

                $quantity = max(1, (int) ($pick['quantity'] ?? $row['quantity'] ?? 1));

                $itemIds[] = $this->putOnBill($invoice, $row, $quantity);
            }

            return $itemIds;
        });
    }

    /**
     * Add one resolved catalogue row to the bill, or top up the line already
     * carrying it.
     *
     * @param  array<string, mixed>  $row
     *
     * @throws ValidationException when the line was already paid for
     */
    private function putOnBill(Invoice $invoice, array $row, int $quantity): int
    {
        $existing = $invoice->items()
            ->where('item_type', $row['item_type'])
            ->when(
                $row['item_id'] !== null,
                fn ($query) => $query->where('item_id', $row['item_id']),
                fn ($query) => $query->where('description', $row['description']),
            )
            ->latest('id')
            ->first();

        if ($existing === null) {
            return $this->billing->addItem($invoice, [
                'item_type' => $row['item_type'],
                'item_id' => $row['item_id'],
                'description' => $row['description'],
                'quantity' => $quantity,
                'unit_price' => round((float) $row['price'], 2),
            ])->id;
        }

        // Already on the bill for at least that many units: the cashier is
        // settling a line that exists, not creating a second one.
        if ($quantity <= (int) $existing->quantity) {
            return $existing->id;
        }

        $unitPrice = round((float) $row['price'], 2);
        $totalPrice = $quantity * $unitPrice;

        if ($existing->isSettled() && (float) $existing->paid_amount + 0.01 >= $totalPrice) {
            throw ValidationException::withMessages([
                'picks' => "{$existing->description} has already been paid for on this bill.",
            ]);
        }

        $existing->forceFill([
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            // The extra units are still owed, so the line goes back into the
            // unpaid bucket instead of hiding behind its old settlement.
            'settled_at' => null,
            'payment_status' => (float) $existing->paid_amount > 0
                ? PaymentStatus::PartiallyPaid
                : PaymentStatus::Pending,
        ])->save();

        $this->billing->recalculateTotals($invoice);

        return $existing->id;
    }

    /**
     * Take the money for the selected charges.
     *
     * @param  array<int, int>  $itemIds
     *
     * @throws ValidationException when nothing is selected, the lines do not
     *                             belong to the patient, or no money was received.
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

            $orderedItems = $items
                ->sortBy(fn (InvoiceItem $item): int => (int) array_search($item->id, $itemIds, true))
                ->values();

            $due = round($orderedItems->sum(
                fn (InvoiceItem $item): float => max(0, (float) $item->total_price - (float) $item->paid_amount),
            ), 2);

            if ($due <= 0) {
                throw ValidationException::withMessages([
                    'item_ids' => 'Those services have no price left to collect.',
                ]);
            }

            $amountTendered = round($amountTendered, 2);

            if ($amountTendered <= 0) {
                throw ValidationException::withMessages([
                    'amount_tendered' => 'Enter the amount received from the patient.',
                ]);
            }

            // A partial payment is real money too: apply it line by line and
            // leave the last covered line `partially_paid`.
            $settledAt = now();
            $remaining = min($amountTendered, $due);

            foreach ($orderedItems as $item) {
                if ($remaining <= 0) {
                    break;
                }

                $lineDue = round(max(0, (float) $item->total_price - (float) $item->paid_amount), 2);

                if ($lineDue <= 0) {
                    continue;
                }

                $applied = round(min($lineDue, $remaining), 2);
                $paid = round((float) $item->paid_amount + $applied, 2);
                $fullyPaid = $paid + 0.01 >= (float) $item->total_price;

                $item->forceFill([
                    'paid_amount' => $paid,
                    'payment_status' => $fullyPaid
                        ? PaymentStatus::Paid
                        : PaymentStatus::PartiallyPaid,
                    'settled_at' => $fullyPaid ? $settledAt : null,
                ])->save();

                $remaining = round($remaining - $applied, 2);
            }

            $totalCollected = round(min($amountTendered, $due), 2);

            $invoice = $items->first()->invoice;

            $payment = CashPayment::create([
                'cash_shift_id' => $shift->id,
                'cash_point_id' => $shift->cash_point_id,
                'invoice_id' => $invoice->id,
                'patient_id' => $patient->id,
                'amount_paid' => $totalCollected,
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
     *
     * A payment that does not cover the whole bill leaves money owed: the
     * invoice becomes overdue so it surfaces in the Invoices ledger instead of
     * hiding in the draft bucket while the patient still owes the rest.
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

        if ($cashPaid > 0 && $hasUnsettled) {
            $this->billing->updateStatus($invoice, InvoiceStatus::OVERDUE);

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
     * The full record behind one invoice: lines, payments, the payer claim and
     * the patient — everything the printable invoice document needs, read from
     * the same relations the counter uses so print and desk never disagree.
     *
     * @return array<string, mixed>
     */
    public function invoiceDetail(Invoice $invoice): array
    {
        $invoice->load([
            'patient:id,first_name,last_name,patient_uid,date_of_birth,gender,address',
            'items' => fn ($query) => $query->orderBy('id'),
            'cashPayments' => fn ($query) => $query->orderBy('id'),
            'claim.payer:id,name,coverage_percentage',
        ]);

        return [
            'id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'status' => $invoice->status->value,
            'invoice_date' => $invoice->invoice_date?->format('Y-m-d'),
            'due_date' => $invoice->due_date?->format('Y-m-d'),
            'paid_at' => $invoice->paid_at?->format('Y-m-d'),
            'subtotal' => (float) $invoice->subtotal,
            'tax_amount' => (float) $invoice->tax_amount,
            'total_amount' => (float) $invoice->total_amount,
            'total_paid' => (float) $invoice->total_paid,
            'remaining_balance' => $invoice->remaining_balance,
            'notes' => $invoice->notes,
            'patient' => $invoice->patient === null ? null : [
                'id' => $invoice->patient->id,
                'first_name' => $invoice->patient->first_name,
                'last_name' => $invoice->patient->last_name,
                'patient_uid' => $invoice->patient->patient_uid,
                'dob' => $invoice->patient->date_of_birth?->format('Y-m-d'),
                'gender' => $invoice->patient->gender?->value,
                'address' => trim((string) $invoice->patient->address),
            ],
            'items' => $invoice->items
                ->map(fn (InvoiceItem $item): array => [
                    'id' => $item->id,
                    'description' => $item->description,
                    'item_type' => $item->item_type,
                    'module' => $this->moduleFor($item->item_type),
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'total_price' => (float) $item->total_price,
                    'paid_amount' => (float) $item->paid_amount,
                    'settled' => $item->isSettled(),
                ])
                ->all(),
            'payments' => $invoice->cashPayments
                ->map(fn (CashPayment $payment): array => [
                    'id' => $payment->id,
                    'amount' => (float) $payment->amount_paid,
                    'method' => $payment->payment_method,
                    'paid_at' => $payment->created_at?->toIso8601String(),
                ])
                ->all(),
            'claim' => $invoice->claim === null ? null : [
                'id' => $invoice->claim->id,
                'status' => $invoice->claim->status->value,
                'claim_amount' => (float) $invoice->claim->claim_amount,
                'payer' => $invoice->claim->payer?->name,
                'coverage' => (float) ($invoice->claim->payer?->coverage_percentage ?? 0),
            ],
        ];
    }

    /**
     * What the desk needs to start a tiers payant claim from a bill.
     *
     * Claiming is a finance capability, so a receptionist who may not see the
     * finance module simply gets `can_claim: false` and no payer list.
     *
     * @return array{can_claim: bool, payers: array<int, array<string, mixed>>, insurances: array<int, array<string, mixed>>}
     */
    public function claimContext(Patient $patient, bool $canClaim): array
    {
        if (! $canClaim) {
            return ['can_claim' => false, 'payers' => [], 'insurances' => []];
        }

        return [
            'can_claim' => true,
            'payers' => Payer::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'coverage_percentage'])
                ->map(fn (Payer $payer): array => [
                    'id' => $payer->id,
                    'name' => $payer->name,
                    'coverage_percentage' => (float) $payer->coverage_percentage,
                ])
                ->all(),
            'insurances' => $patient->patientInsurances()
                ->with('payer:id,name')
                ->get()
                ->map(fn ($insurance): array => [
                    'id' => $insurance->id,
                    'payer_id' => $insurance->payer_id,
                    'payer' => $insurance->payer?->name,
                    'policy_number' => $insurance->policy_number,
                    'is_primary' => (bool) $insurance->is_primary,
                ])
                ->all(),
        ];
    }

    /**
     * Map a billable line to the module that owns the service.
     */
    private function moduleFor(?string $itemType): string
    {
        return match ($itemType) {
            'lab_test', 'laboratory', 'lab' => 'laboratory',
            'pharmacy', 'medication' => 'pharmacy',
            'radiology', 'imaging' => 'radiology',
            'consultation', 'appointment' => 'consultation',
            'admission', 'bed', 'observation', 'admission_service' => 'admission',
            'surgery' => 'surgery',
            'emergency' => 'emergency',
            'procedure' => 'procedure',
            'nursing' => 'nursing',
            default => 'other',
        };
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
