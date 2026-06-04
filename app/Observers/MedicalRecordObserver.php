<?php

namespace App\Observers;

use App\Models\MedicalRecord;
use App\Services\Billing\BillingService;

class MedicalRecordObserver
{
    public function created(MedicalRecord $record): void
    {
        $billing = app(BillingService::class);
        $invoice = $billing->findOrCreateDraftInvoice($record->patient_id);
        $billing->addBillableItem(
            $invoice,
            'consultation',
            $record->id,
            'Medical Record: Consultation',
            0,
        );
    }
}
