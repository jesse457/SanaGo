<?php

namespace App\Observers;

use App\Enums\LabRequestStatus;
use App\Models\LabRequest;
use App\Services\Billing\BillingService;
use App\Services\System\NotificationService;

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

            // Notify the requesting doctor
            $patientName = $request->patient?->first_name.' '.$request->patient?->last_name;
            $testName = $request->testDefinition?->test_name ?? 'Unknown test';

            if ($request->doctor) {
                app(NotificationService::class)->notifyUser(
                    $request->doctor,
                    message: "Lab result ready: {$testName} for {$patientName}",
                    category: 'lab',
                    link: '/lab/results',
                    typeLabel: 'success',
                );
            }
        }
    }
}
