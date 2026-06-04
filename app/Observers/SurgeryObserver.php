<?php

namespace App\Observers;

use App\Enums\SurgeryStatus;
use App\Models\Surgery;
use App\Services\Billing\BillingService;

class SurgeryObserver
{
    public function updated(Surgery $surgery): void
    {
        if (
            $surgery->status === SurgeryStatus::COMPLETED
            && $surgery->getOriginal('status') !== SurgeryStatus::COMPLETED->value
        ) {
            $billing = app(BillingService::class);
            $invoice = $billing->findOrCreateDraftInvoice($surgery->patient_id);
            $billing->addBillableItem(
                $invoice,
                'surgery',
                $surgery->id,
                "Surgery: {$surgery->surgery_name}",
                (float) $surgery->surgery_fee,
            );
        }
    }
}
