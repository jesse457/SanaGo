<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            // 1. Patient Details (Safe handling if patient is deleted)
            'patient' => [
                'id' => $this->patient_id,
                'full_name' => $this->patient
                    ? "{$this->patient->first_name} {$this->patient->last_name}"
                    : 'Unknown Patient',
                'first_name' => $this->patient?->first_name,
                'last_name' => $this->patient?->last_name,
                'gender' => $this->patient?->gender,
                'phone' => $this->patient?->phone,
            ],

            // 2. Doctor Details
            'doctor' => [
                'id' => $this->doctor_id,
                'name' => $this->doctor?->name ?? 'Unassigned',
            ],

            // 3. Scheduling
            // Format date as YYYY-MM-DD
            'date' => $this->appointment_date ? $this->appointment_date->format('Y-m-d') : null,
            // Format time as HH:MM (e.g., 14:30)
            'time' => $this->appointment_time ? $this->appointment_time->format('H:i') : null,

            // 4. Status & Queue
            'status' => $this->status, // Waiting, In Consultation, Completed, Canceled
            'queue_position' => $this->queue_position,

            // 5. Clinical / Financial Context
            'reason' => $this->reason_for_visit,
            'notes' => $this->notes,
            'price' => (float) $this->price,

            // 6. Audit Timestamps (Useful for calculating duration)
            'timings' => [
                'actual_start' => $this->actual_start_time, // Returns ISO string or null
                'actual_end' => $this->actual_end_time,
                'created_at' => $this->created_at->diffForHumans(),
            ],
        ];
    }
}
