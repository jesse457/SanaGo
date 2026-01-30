<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LabRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'requested_by_doctor_id' => $this->requested_by_doctor_id,
            'lab_test_definition_id' => $this->lab_test_definition_id,
            'consultation_id' => $this->consultation_id,
            'reason_for_test' => $this->reason_for_test,
            'urgency_level' => $this->urgency_level,
            'request_date' => $this->request_date?->toDateTimeString(),
            'status' => $this->status,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),

            // Relationships
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'doctor' => new UserResource($this->whenLoaded('doctor')),
            'test_definition' => $this->whenLoaded('testDefinition'),
            'result' => $this->whenLoaded('result'),
        ];
    }
}
