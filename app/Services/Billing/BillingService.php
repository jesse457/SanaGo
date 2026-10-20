<?php

namespace App\Services\Billing;

use App\Enums\ClaimStatus;
use App\Enums\InvoiceStatus;
use App\Models\Admission;
use App\Models\CashPayment;
use App\Models\CashShift;
use App\Models\Claim;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;

class BillingService
{
    public function generateInvoiceNumber(): string
    {
        $prefix = config('billing.invoice_prefix', 'INV').'-'.date('ym');
        $latest = Invoice::where('invoice_number', 'like', $prefix.'%')
            ->orderBy('id', 'desc')
            ->first();

        if ($latest) {
            $seq = (int) substr($latest->invoice_number, -4) + 1;
        } else {
            $seq = 1;
        }

        return $prefix.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * The note carried by the invoice generated for an admission at discharge.
     * AdmissionObserver uses it to detect an already billed discharge.
     */
    public static function admissionInvoiceNote(Admission $admission): string
    {
        return "Invoice generated at discharge for admission #{$admission->id}";
    }

    public function createInvoiceWithItems(array $invoiceData, array $items): Invoice
    {
        return DB::transaction(function () use ($invoiceData, $items) {
            $invoiceData['invoice_number'] = $this->generateInvoiceNumber();
            $invoiceData['status'] = $invoiceData['status'] ?? InvoiceStatus::DRAFT->value;
            $invoiceData['subtotal'] = 0;
            $invoiceData['tax_amount'] = 0;
            $invoiceData['total_amount'] = 0;

            $invoice = Invoice::create($invoiceData);

            foreach ($items as $item) {
                $item['total_price'] = $item['quantity'] * $item['unit_price'];
                $invoice->items()->create($item);
            }

            return $this->recalculateTotals($invoice);
        });
    }

    public function addItem(Invoice $invoice, array $itemData): InvoiceItem
    {
        $itemData['total_price'] = $itemData['quantity'] * $itemData['unit_price'];
        $item = $invoice->items()->create($itemData);
        $this->recalculateTotals($invoice);

        return $item;
    }

    public function removeItem(Invoice $invoice, InvoiceItem $item): void
    {
        $item->delete();
        $this->recalculateTotals($invoice);
    }

    public function updateStatus(Invoice $invoice, InvoiceStatus $status): Invoice
    {
        $invoice->status = $status;

        if ($status === InvoiceStatus::PAID) {
            $invoice->paid_at = now();
        }

        $invoice->save();

        return $invoice;
    }

    public function recalculateTotals(Invoice $invoice): Invoice
    {
        $subtotal = $invoice->items()->sum('total_price');
        $taxAmount = 0;
        $totalAmount = $subtotal + $taxAmount;

        $invoice->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
        ]);

        return $invoice->fresh();
    }

    public function createInvoice(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            return Invoice::create($data);
        });
    }

    public function updateInvoice(Invoice $invoice, array $data): Invoice
    {
        $invoice->update($data);

        return $invoice;
    }

    public function findOrCreateDraftInvoice(int $patientId): Invoice
    {
        return Invoice::firstOrCreate(
            [
                'patient_id' => $patientId,
                'status' => InvoiceStatus::DRAFT->value,
            ],
            [
                'invoice_number' => $this->generateInvoiceNumber(),
                'invoice_date' => now()->format('Y-m-d'),
                'subtotal' => 0,
                'tax_amount' => 0,
                'total_amount' => 0,
            ]
        );
    }

    public function addBillableItem(
        Invoice $invoice,
        string $itemType,
        ?int $itemId,
        string $description,
        float $unitPrice,
        int $quantity = 1,
    ): ?InvoiceItem {
        if ($itemId !== null) {
            $existing = $invoice->items()
                ->where('item_type', $itemType)
                ->where('item_id', $itemId)
                ->first();

            if ($existing) {
                return null;
            }
        }

        return $this->addItem($invoice, [
            'item_type' => $itemType,
            'item_id' => $itemId,
            'description' => $description,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
        ]);
    }

    /**
     * Record a partial cash payment against an invoice.
     * Auto-updates invoice status to PAID when the cash portion covers the bill.
     *
     * `is_fully_paid` counts settled insurance claims as money received, so it
     * would mark an invoice PAID while the patient still owes the cash share.
     * The decision here compares the cash collected against the amount due.
     */
    public function recordPayment(Invoice $invoice, float $amount, int $cashShiftId, string $paymentMethod = 'cash'): CashPayment
    {
        return DB::transaction(function () use ($invoice, $amount, $cashShiftId, $paymentMethod) {
            $shift = CashShift::find($cashShiftId);

            $payment = CashPayment::create([
                'cash_shift_id' => $cashShiftId,
                'cash_point_id' => $shift?->cash_point_id,
                'invoice_id' => $invoice->id,
                'patient_id' => $invoice->patient_id,
                'amount_paid' => $amount,
                'payment_method' => $paymentMethod,
            ]);

            // Refresh invoice to recalculate total_paid
            $invoice->refresh();

            $cashPaid = (float) $invoice->cashPayments()->sum('amount_paid');
            $amountDue = (float) $invoice->total_amount;

            // `is_fully_paid` counts settled insurance claims as money received,
            // so on its own it would close an invoice whose cash share is still
            // outstanding — and mark it PAID off a zero-value cash payment.
            // Require an actual cash payment before trusting the settled total.
            $settledInFull = $invoice->is_fully_paid && $cashPaid > 0;

            if ($settledInFull || $cashPaid >= $amountDue) {
                $this->updateStatus($invoice, InvoiceStatus::PAID);
            } elseif ($invoice->status === InvoiceStatus::DRAFT) {
                $this->updateStatus($invoice, InvoiceStatus::PENDING);
            }

            return $payment;
        });
    }

    /**
     * Generate an invoice from an admission at discharge.
     * Collects all admission_services, adds bed charges, calculates insurance split.
     */
    public function generateInvoiceFromAdmission(Admission $admission): Invoice
    {
        return DB::transaction(function () use ($admission) {
            $patient = $admission->patient;
            $admissionServices = $admission->admissionServices()->with('service')->get();

            // Create invoice
            $invoice = Invoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'patient_id' => $patient->id,
                'invoice_date' => now()->format('Y-m-d'),
                'due_date' => now()->addDays(30)->format('Y-m-d'),
                'subtotal' => 0,
                'tax_amount' => 0,
                'total_amount' => 0,
                'status' => InvoiceStatus::PENDING->value,
                'notes' => self::admissionInvoiceNote($admission),
            ]);

            // Add admission services as invoice items
            foreach ($admissionServices as $as) {
                $service = $as->service;
                $invoice->items()->create([
                    'item_type' => 'admission_service',
                    'item_id' => $as->id,
                    'description' => $service ? $service->name : 'Service',
                    'quantity' => $as->quantity,
                    'unit_price' => $as->unit_price,
                    'total_price' => $as->total_price,
                ]);
            }

            // Add bed charges if applicable
            if ($admission->bed && $admission->bed->bedType) {
                $admissionDate = $admission->admission_date ?? now()->startOfDay();
                $dischargeDate = $admission->discharge_date ?? now();
                $nights = max(1, (int) $admissionDate->diffInDays($dischargeDate));
                $dailyRate = (float) ($admission->bed->bedType->daily_rate ?? 0);

                if ($dailyRate > 0) {
                    $bedTotal = $nights * $dailyRate;
                    $invoice->items()->create([
                        'item_type' => 'bed',
                        'item_id' => $admission->bed_id,
                        'description' => "Lit: {$admission->bed->bedType->name} ({$nights} nuit(s) × {$dailyRate} XAF)",
                        'quantity' => $nights,
                        'unit_price' => $dailyRate,
                        'total_price' => $bedTotal,
                    ]);
                }
            }

            // Add observation fee if set
            if ((float) $admission->observation_fee > 0) {
                $invoice->items()->create([
                    'item_type' => 'observation',
                    'item_id' => null,
                    'description' => 'Frais d\'observation médicale',
                    'quantity' => 1,
                    'unit_price' => (float) $admission->observation_fee,
                    'total_price' => (float) $admission->observation_fee,
                ]);
            }

            // Calculate totals
            $this->recalculateTotals($invoice);

            // Handle insurance: find primary patient insurance
            $primaryInsurance = $patient->patientInsurances()
                ->where('is_primary', true)
                ->with('payer')
                ->first();

            if ($primaryInsurance && $primaryInsurance->payer) {
                $payer = $primaryInsurance->payer;
                $coveragePercent = (float) $payer->coverage_percentage;

                if ($coveragePercent > 0) {
                    $claimAmount = round($invoice->total_amount * ($coveragePercent / 100), 2);

                    // Create claim for insurance portion
                    Claim::create([
                        'invoice_id' => $invoice->id,
                        'payer_id' => $payer->id,
                        'patient_insurance_id' => $primaryInsurance->id,
                        'claim_amount' => $claimAmount,
                        'status' => ClaimStatus::Draft,
                    ]);
                }
            }

            return $invoice->fresh(['items', 'claim', 'cashPayments']);
        });
    }
}
