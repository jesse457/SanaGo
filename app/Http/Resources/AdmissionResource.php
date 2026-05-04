<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdmissionResource extends JsonResource
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
            'status' => $this->status, // e.g., Admitted, Discharged, Pending
            'reason' => $this->reason_for_admission,

            // Financials
            'observation_fee' => (float) $this->observation_fee,

            // Dates & Duration
            'admission_date' => $this->admission_date ? $this->admission_date->format('Y-m-d H:i') : null,
            'discharge_date' => $this->discharge_date ? $this->discharge_date->format('Y-m-d H:i') : null,
            'days_admitted' => $this->calculateDuration(),

            // Patient Details (Nested or ID only)
            'patient' => [
                'id' => $this->patient_id,
                'full_name' => $this->patient ? "{$this->patient->first_name} {$this->patient->last_name}" : 'N/A',
                'uid' => $this->patient?->patient_uid,
                'gender' => $this->patient?->gender,
            ],

            // Doctor Details
            'doctor' => [
                'id' => $this->doctor_id,
                'name' => $this->doctor?->name ?? 'Unassigned',
            ],

            // Bed / Ward Details
            'bed' => [
                'id' => $this->bed_id,
                'code' => $this->bed?->bed_number ?? $this->bed?->code ?? 'N/A',
                // Assuming your Bed model has a ward relationship
                'ward' => $this->bed?->ward?->name ?? 'N/A',
            ],

            // Audit
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }

    /**
     * Helper to calculate how many days the patient has been admitted.
     */
    private function calculateDuration(): int
    {
        if (! $this->admission_date) {
            return 0;
        }

        $end = $this->discharge_date ?? now();

        return (int) $this->admission_date->diffInDays($end);
    }
}
