<?php

namespace App\Services\PatientManagement;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    public function book(array $validated): Appointment
    {
        return DB::transaction(function () use ($validated) {
            $exists = Appointment::where('patient_id', $validated['patient_id'])
                ->where('doctor_id', $validated['doctor_id'])
                ->whereDate('appointment_date', $validated['appointment_date'])
                ->whereIn('status', [
                    AppointmentStatus::WAITING->value,
                    AppointmentStatus::IN_CONSULTATION->value,
                    AppointmentStatus::PENDING->value,
                ])
                ->exists();

            if ($exists) {
                throw new \Exception('This patient is already in the queue for this doctor today.');
            }

            $latest = Appointment::where('doctor_id', $validated['doctor_id'])
                ->whereDate('appointment_date', $validated['appointment_date'])
                ->lockForUpdate()
                ->max('queue_position');

            $position = ($latest ?? 0) + 1;

            return Appointment::create([
                'patient_id' => $validated['patient_id'],
                'doctor_id' => $validated['doctor_id'],
                'appointment_date' => $validated['appointment_date'],
                'appointment_time' => $validated['appointment_time'],
                'reason_for_visit' => $validated['reason_for_visit'],
                'consultation_fee' => $validated['consultation_fee'] ?? 0,
                'status' => AppointmentStatus::WAITING->value,
                'queue_position' => $position,
            ]);
        });
    }
}
