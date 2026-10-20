<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Services\Billing\BillingService;
use App\Services\Billing\PriceListService;

/**
 * An appointment is billed as a *consultation*, at the doctor's own price.
 *
 * There is no separate "appointment" product: when a patient calls to book and
 * pays, that money is a consultation fee. The charge is created when the
 * appointment is booked — not when it is marked completed — because the counter
 * collects before the patient sees the doctor.
 *
 * A `consultation_fee` already stored on the appointment wins, so a negotiated
 * price is never overwritten by the catalogue.
 */
class AppointmentObserver
{
    public function created(Appointment $appointment): void
    {
        $prices = app(PriceListService::class);
        $billing = app(BillingService::class);

        $doctor = $appointment->doctor;
        $storedFee = (float) ($appointment->consultation_fee ?? 0);

        $price = $storedFee > 0
            ? $storedFee
            : $prices->consultationPrice($doctor);

        // Nothing to collect when no consultation price is configured yet.
        if ($price <= 0) {
            return;
        }

        $invoice = $billing->findOrCreateDraftInvoice($appointment->patient_id);

        $billing->addBillableItem(
            $invoice,
            'consultation',
            $appointment->id,
            $doctor === null
                ? 'Consultation'
                : 'Consultation — Dr. '.$doctor->name,
            $price,
        );
    }
}
