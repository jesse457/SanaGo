<?php

namespace App\Services\PatientManagement;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;

class AppointmentService
{
    /**
     * Book an appointment.
     *
     * Appointments are scheduled visits. They carry no queue state and know
     * nothing about other modules: billing the visit is the responsibility of
     * the billing domain, which reacts when the consultation is recorded.
     */
    public function book(array $validated): Appointment
    {
        $exists = Appointment::where('patient_id', $validated['patient_id'])
            ->where('doctor_id', $validated['doctor_id'])
            ->where('appointment_date', $validated['appointment_date'])
            ->whereIn('status', [
                AppointmentStatus::SCHEDULED->value,
                AppointmentStatus::PENDING->value,
                AppointmentStatus::IN_CONSULTATION->value,
            ])
            ->exists();

        if ($exists) {
            throw new \Exception('This patient already has an appointment with this doctor on that date.');
        }

        return Appointment::create([
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $validated['doctor_id'],
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'reason_for_visit' => $validated['reason_for_visit'] ?? null,
            'consultation_fee' => $validated['consultation_fee'] ?? 0,
            'status' => AppointmentStatus::SCHEDULED->value,
        ]);
    }
}
