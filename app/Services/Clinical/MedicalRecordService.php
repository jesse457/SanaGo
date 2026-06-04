<?php

namespace App\Services\Clinical;

use App\Enums\LabRequestStatus;
use App\Enums\PrescriptionStatus;
use App\Models\LabRequest;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class MedicalRecordService
{
    public function createConsultation(array $validated, ?array $attachments = null): MedicalRecord
    {
        $doctorId = auth()->id();

        return DB::transaction(function () use ($validated, $doctorId, $attachments) {
            $medicalRecord = MedicalRecord::create([
                'patient_id' => $validated['patient_id'],
                'doctor_id' => $doctorId,
                'record_date' => now(),
                'chief_complaint' => $validated['chief_complaint'],
                'history_of_present_illness' => $validated['history_of_present_illness'] ?? null,
                'physical_exam' => $validated['physical_exam'] ?? null,
                'diagnosis' => $validated['diagnosis'] ?? null,
                'treatment_plan' => $validated['treatment_plan'] ?? null,
            ]);

            if ($attachments) {
                foreach ($attachments as $file) {
                    if ($file instanceof UploadedFile) {
                        $medicalRecord->addMedia($file)
                            ->toMediaCollection('attachments');
                    }
                }
            }

            if (! empty($validated['prescription_items'])) {
                $prescription = Prescription::create([
                    'patient_id' => $validated['patient_id'],
                    'doctor_id' => $doctorId,
                    'prescription_date' => now(),
                    'status' => PrescriptionStatus::NEW->value,
                ]);

                foreach ($validated['prescription_items'] as $item) {
                    $prescription->items()->create([
                        'medication_id' => $item['medication_id'],
                        'drug_name' => $item['drug_name'],
                        'dosage' => $item['dosage'],
                        'frequency' => $item['frequency'],
                        'duration' => $item['duration'],
                        'quantity' => $item['quantity'],
                        'instructions' => $item['instructions'] ?? null,
                    ]);
                }
            }

            if (! empty($validated['lab_requests'])) {
                foreach ($validated['lab_requests'] as $lab) {
                    LabRequest::create([
                        'patient_id' => $validated['patient_id'],
                        'doctor_id' => $doctorId,
                        'consultation_id' => $medicalRecord->id,
                        'test_definition_id' => $lab['test_definition_id'],
                        'request_date' => now(),
                        'status' => LabRequestStatus::PENDING->value,
                        'urgency' => $lab['urgency'],
                        'reason_for_test' => $lab['reason_for_test'] ?? null,
                    ]);
                }
            }

            return $medicalRecord;
        });
    }
}
