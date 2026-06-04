<?php

namespace App\Observers;

use App\Enums\PrescriptionStatus;
use App\Models\Prescription;
use App\Services\Billing\BillingService;

class PrescriptionObserver
{
    public function updated(Prescription $prescription): void
    {
        if (
            $prescription->status === PrescriptionStatus::COMPLETED
            && $prescription->getOriginal('status') !== PrescriptionStatus::COMPLETED->value
        ) {
            $totalAmount = $prescription->total_amount
                ?? $prescription->items->sum(fn ($item) => ($item->unit_price ?? 0) * $item->quantity);

            $billing = app(BillingService::class);
            $invoice = $billing->findOrCreateDraftInvoice($prescription->patient_id);
            $billing->addBillableItem(
                $invoice,
                'pharmacy',
                $prescription->id,
                "Prescription #{$prescription->id}",
                (float) $totalAmount,
            );
        }
    }
}
