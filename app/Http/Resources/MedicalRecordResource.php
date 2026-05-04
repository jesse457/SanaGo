<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MedicalRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'diagnosis_text' => $this->diagnosis_text,
            'general_notes' => $this->general_notes,

            'doctor' => $this->whenLoaded('doctor'),
            'patient' => $this->whenLoaded('patient'),

            // FIX: Use map() because 'prescription' is a Collection (HasMany)
            'prescriptions' => $this->whenLoaded('prescription', function () {
                // If it's an empty collection, return empty array
                if ($this->prescription->isEmpty()) {
                    return [];
                }

                return $this->prescription->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'created_at' => $p->created_at,
                        'items' => $p->items->map(fn ($item) => [
                            'drug_name' => $item->medication->name ?? $item->drug_name,
                            'dosage' => $item->dosage,
                            'frequency' => $item->frequency,
                            'duration' => $item->duration,
                            'quantity_prescribed' => $item->quantity_prescribed ?? 0,
                        ]),
                    ];
                });
            }),

            // Lab Results Logic
            'lab_results' => $this->whenLoaded('labResults', function () {
                return $this->labResults->map(function ($result) {
                    return [
                        'id' => $result->id,
                        'created_at' => $result->created_at,
                        // Handle potential nulls in relationships
                        'test_name' => $result->labRequest->testDefinition->test_name ?? 'Lab Result',
                        'technician_name' => $result->labTechnician->name ?? 'Technician',
                        'results_text' => $result->results_text,
                        'analysis_comments' => $result->analysis_comments,
                        'status' => $result->status ?? 'Completed',

                        // Attachments
                        'attachments' => $result->attachments->map(fn ($att) => [
                            'id' => $att->id,
                            'file_name' => $att->file_name,
                            'file_url' => config('filesystems.default') === 's3'
                                ? Storage::disk('s3')->temporaryUrl($att->file_path, now()->addMinutes(20))
                                : route('doctor.attachments.preview', $att->id),
                        ]),
                    ];
                });
            }),
        ];
    }
}
