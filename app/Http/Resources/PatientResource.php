<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
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
            'uid' => $this->patient_uid,

            // Names
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => "{$this->first_name} {$this->last_name}",
            'initials' => $this->getInitials(),

            // Contact & Bio
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'age' => (int) $this->age,
            'gender' => $this->gender,

            // Statuses
            'is_admitted_approve' => (bool) $this->is_admitted_approve,

            // Paths
            'referral_note_path' => $this->referral_note_path,

            // Timestamps
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'registered_since' => $this->created_at?->diffForHumans(),

            // Conditional Relationships (Only included if loaded via ->with())
            'appointments' => AppointmentResource::collection($this->whenLoaded('appointments')),
            'appointments_count' => $this->whenCounted('appointments'),
            'last_appointment' => new AppointmentResource($this->whenLoaded('lastAppointment')),
        ];
    }

    /**
     * Helper to generate initials for UI Avatars.
     */
    private function getInitials(): string
    {
        $first = mb_substr($this->first_name ?? '', 0, 1);
        $last = mb_substr($this->last_name ?? '', 0, 1);

        return strtoupper($first.$last);
    }
}
