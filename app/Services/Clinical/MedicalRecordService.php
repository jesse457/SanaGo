<?php

namespace App\Services\Clinical;

use App\Enums\LabRequestStatus;
use App\Enums\PrescriptionStatus;
use App\Models\LabRequest;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\Vital;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class MedicalRecordService
{
    public function createConsultation(array $validated, ?array $attachments = null): MedicalRecord
    {
        $doctorId = auth()->id();

        return DB::transaction(function () use ($validated, $doctorId, $attachments) {

            // ── 1. Create Medical Record ──
            $medicalRecord = MedicalRecord::create([
                'patient_id' => $validated['patient_id'],
                'doctor_id' => $doctorId,
                'record_date' => now(),
                'chief_complaint' => $validated['chief_complaint'],
                'history_of_present_illness' => $validated['history_of_present_illness'] ?? null,
                'physical_exam' => $validated['physical_exam'] ?? null,
                'diagnosis' => $validated['diagnosis'] ?? null,
                'treatment_plan' => $validated['treatment_plan'] ?? null,
                'notes' => $validated['notes'] ?? null,   // ← was missing
            ]);

            // ── 2. Attach Files ──
            if ($attachments) {
                foreach ($attachments as $file) {
                    if ($file instanceof UploadedFile && $file->isValid()) {
                        $medicalRecord->addMedia($file)
                            ->toMediaCollection('attachments');
                    }
                }
            }

            // ── 3. Create Prescription + Items ──
            if (! empty($validated['prescription_items'])) {
                $prescription = Prescription::create([
                    'patient_id' => $validated['patient_id'],
                    'doctor_id' => $doctorId,
                    'prescription_date' => now(),
                    'status' => PrescriptionStatus::NEW->value,
                ]);

                foreach ($validated['prescription_items'] as $item) {
                    $prescription->items()->create([
                        'medication_id' => $item['medication_id'] ?? null,  // ← null for external drugs
                        'drug_name' => $item['drug_name'],
                        'dosage' => $item['dosage'] ?? null,
                        'frequency' => $item['frequency'] ?? null,
                        'duration' => $item['duration'] ?? null,
                        'quantity' => $item['quantity'],
                        'instructions' => $item['instructions'] ?? null,
                    ]);
                }
            }

            // ── 4. Create Lab Requests ──
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

            // ── 5. Record Vital Signs (if any were provided) ──
            $hasVitals = ! empty($validated['temperature']) || ! empty($validated['systolic_bp'])
                || ! empty($validated['heart_rate']) || ! empty($validated['oxygen_saturation']);

            if ($hasVitals) {
                Vital::create([
                    'patient_id' => $validated['patient_id'],
                    'recorded_by' => $doctorId,
                    'recorded_at' => now(),
                    'temperature' => $validated['temperature'] ?? null,
                    'systolic_bp' => $validated['systolic_bp'] ?? null,
                    'diastolic_bp' => $validated['diastolic_bp'] ?? null,
                    'heart_rate' => $validated['heart_rate'] ?? null,
                    'oxygen_saturation' => $validated['oxygen_saturation'] ?? null,
                    'respiratory_rate' => $validated['respiratory_rate'] ?? null,
                    'is_abnormal' => $this->flagAbnormalVitals($validated),
                    'notes' => $this->formatVitalsNote($validated),
                ]);
            }

            // ── 6. Update Patient's last_visit_date ──
            $medicalRecord->patient()->update([
                'last_visit_date' => now(),
            ]);

            return $medicalRecord;
        });
    }

    /**
     * Flag vitals as abnormal if any value is outside normal range.
     */
    private function flagAbnormalVitals(array $data): bool
    {
        $temp = $data['temperature'] ?? null;
        $sys = $data['systolic_bp'] ?? null;
        $dia = $data['diastolic_bp'] ?? null;
        $hr = $data['heart_rate'] ?? null;
        $spo2 = $data['oxygen_saturation'] ?? null;
        $rr = $data['respiratory_rate'] ?? null;

        $thresholds = config('clinical.vitals');

        if ($temp !== null && ($temp < $thresholds['temperature']['min'] || $temp > $thresholds['temperature']['max'])) {
            return true;
        }
        if ($sys !== null && ($sys < $thresholds['systolic_bp']['min'] || $sys > $thresholds['systolic_bp']['max'])) {
            return true;
        }
        if ($dia !== null && ($dia < $thresholds['diastolic_bp']['min'] || $dia > $thresholds['diastolic_bp']['max'])) {
            return true;
        }
        if ($hr !== null && ($hr < $thresholds['heart_rate']['min'] || $hr > $thresholds['heart_rate']['max'])) {
            return true;
        }
        if ($spo2 !== null && $spo2 < $thresholds['oxygen_saturation']['min']) {
            return true;
        }
        if ($rr !== null && ($rr < $thresholds['respiratory_rate']['min'] || $rr > $thresholds['respiratory_rate']['max'])) {
            return true;
        }

        return false;
    }

    /**
     * Format weight/height info into vitals notes.
     */
    private function formatVitalsNote(array $data): ?string
    {
        $parts = [];

        if (! empty($data['weight_kg'])) {
            $parts[] = "Weight: {$data['weight_kg']} kg";
        }
        if (! empty($data['height_cm'])) {
            $parts[] = "Height: {$data['height_cm']} cm";
        }
        if (! empty($data['follow_up_date'])) {
            $parts[] = "Follow-up: {$data['follow_up_date']}";
        }
        if (! empty($data['follow_up_instructions'])) {
            $parts[] = "Follow-up instructions: {$data['follow_up_instructions']}";
        }

        return $parts ? implode(' | ', $parts) : null;
    }
}
