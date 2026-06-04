<?php

namespace App\Observers;

use App\Enums\LabRequestStatus;
use App\Models\LabRequest;
use App\Services\Billing\BillingService;

class LabRequestObserver
{
    public function updated(LabRequest $request): void
    {
        if (
            $request->status === LabRequestStatus::COMPLETED
            && $request->getOriginal('status') !== LabRequestStatus::COMPLETED->value
        ) {
            $price = $request->testDefinition?->price ?? 0;
            $billing = app(BillingService::class);
            $invoice = $billing->findOrCreateDraftInvoice($request->patient_id);
            $billing->addBillableItem(
                $invoice,
                'lab_test',
                $request->id,
                "Lab Test: {$request->testDefinition?->test_name}",
                (float) $price,
            );
        }
    }
}
