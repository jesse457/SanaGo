<?php

namespace App\Observers;

use App\Enums\LabRequestStatus;
use App\Models\LabRequest;
use App\Services\System\NotificationService;

/**
 * Laboratory — an order is a clinical fact, not a bill.
 *
 * A lab request never mints an invoice line. The charge a patient pays before
 * the tube is drawn is written at the counter (the Service catalogue or a
 * hand-written charge), so the laboratory works on orders, not on money. The
 * only server hook left is the notification a doctor receives when a result is
 * verified.
 */
class LabRequestObserver
{
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
