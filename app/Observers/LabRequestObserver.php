<?php

namespace App\Observers;

use App\Enums\LabRequestStatus;
use App\Models\LabRequest;
use App\Services\Billing\BillingService;
use App\Services\System\NotificationService;

/**
 * A lab test is billed when it is ordered, not when the result is ready.
 *
 * In a Cameroon hospital the patient pays at the caisse *before* going to the
 * laboratory. Charging only on completion left the counter with nothing to
 * collect, so the cashier had to type an amount by hand and the laboratory had
 * no way of knowing whether the test had been paid for.
 *
 * The line stays unpaid (`settled_at` null) until the counter settles it; the
 * completion hook now only notifies the requesting doctor.
 */
class LabRequestObserver
{
    public function created(LabRequest $request): void
    {
        $price = (float) ($request->testDefinition?->price ?? 0);

        // A free test needs no counter visit.
        if ($price <= 0) {
            return;
        }

        $billing = app(BillingService::class);
        $invoice = $billing->findOrCreateDraftInvoice($request->patient_id);

        $billing->addBillableItem(
            $invoice,
            'lab_test',
            $request->id,
            'Lab Test: '.($request->testDefinition?->test_name ?? 'Unknown test'),
            $price,
        );
    }

    public function updated(LabRequest $request): void
    {
        if (
            $request->status === LabRequestStatus::COMPLETED
            && $request->getOriginal('status') !== LabRequestStatus::COMPLETED->value
        ) {
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
