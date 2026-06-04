<?php

namespace App\Observers;

use App\Enums\AdmissionStatus;
use App\Models\Admission;
use App\Services\Billing\BillingService;

class AdmissionObserver
{
    public function updated(Admission $admission): void
    {
        if (
            $admission->status === AdmissionStatus::Discharged
            && $admission->getOriginal('status') !== AdmissionStatus::Discharged->value
        ) {
            $billing = app(BillingService::class);
            $invoice = $billing->findOrCreateDraftInvoice($admission->patient_id);
            $billing->addBillableItem(
                $invoice,
                'admission',
                $admission->id,
                'Admission (Discharged)',
                (float) $admission->observation_fee,
            );
        }
    }
}
