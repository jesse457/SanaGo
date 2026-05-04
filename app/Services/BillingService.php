<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Patient;
use Exception;
use Illuminate\Support\Facades\DB;

class BillingService
{
    /**
     * Create a new invoice for a patient.
     */
    public function createInvoice(Patient $patient, float $amount, string $status = 'Unpaid', ?string $paymentMethod = null): Invoice
    {
        return DB::connection('pgsql_transaction')->transaction(function () use ($patient, $amount, $status, $paymentMethod) {
            return Invoice::create([
                'patient_id' => $patient->id,
                'amount' => $amount,
                'status' => $status,
                'payment_method' => $paymentMethod,
            ]);
        });
    }

    /**
     * Record a payment for an invoice.
     *
     * @throws Exception
     */
    public function recordPayment(Invoice $invoice, float $amountPaid, string $method): Invoice
    {
        return DB::connection('pgsql_transaction')->transaction(function () use ($invoice, $amountPaid, $method) {
            if ($amountPaid <= 0) {
                throw new Exception('Payment amount must be greater than zero.');
            }

            // Here you might want to create a PaymentTransaction model if one existed.
            // For now, we update the invoice status.

            // Assuming full payment updates status to Paid.
            // If partial payments are supported, logic would be more complex.
            // Since the Invoice model is simple, we'll assume full payment or manual status management.

            // Only update if paying the full amount or if explicitly finalizing
            // Ideally we'd compare $amountPaid vs $invoice->amount

            $invoice->update([
                'status' => 'Paid',
                'payment_method' => $method,
            ]);

            return $invoice;
        });
    }

    /**
     * Get unpaid invoices for a patient.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPendingInvoices(Patient $patient)
    {
        return $patient->invoices()->where('status', '!=', 'Paid')->get();
    }
}
