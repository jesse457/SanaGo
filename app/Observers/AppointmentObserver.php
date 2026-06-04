<?php

namespace App\Observers;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Services\Billing\BillingService;

class AppointmentObserver
{
    public function updated(Appointment $appointment): void
    {
        if (
            $appointment->status === AppointmentStatus::COMPLETED
            && $appointment->getOriginal('status') !== AppointmentStatus::COMPLETED->value
        ) {
            $billing = app(BillingService::class);
            $invoice = $billing->findOrCreateDraftInvoice($appointment->patient_id);
            $billing->addBillableItem(
                $invoice,
                'consultation',
                $appointment->id,
                'Appointment Consultation',
                (float) $appointment->consultation_fee,
            );
        }
    }
}
