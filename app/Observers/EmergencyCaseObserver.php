<?php

namespace App\Observers;

use App\Enums\EmergencyStatus;
use App\Models\EmergencyCase;
use App\Services\Billing\BillingService;

class EmergencyCaseObserver
{
    public function updated(EmergencyCase $case): void
    {
        $billable = [EmergencyStatus::STABILIZED->value, EmergencyStatus::DISCHARGED->value];
        $original = $case->getOriginal('status');

        if (in_array($case->status->value, $billable) && ! in_array($original, $billable)) {
            $billing = app(BillingService::class);
            $invoice = $billing->findOrCreateDraftInvoice($case->patient_id);
            $billing->addBillableItem(
                $invoice,
                'emergency',
                $case->id,
                "Emergency Case: {$case->chief_complaint}",
                (float) $case->emergency_fee,
            );
        }
    }
}
