<?php

namespace App\Services;

use App\Models\NurseCareReport;
use App\Models\Patient;
use App\Models\Vital;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NurseService
{
    /**
     * Get all patients for dropdown.
     */
    public function getPatients()
    {
        return Patient::orderBy('first_name')->orderBy('last_name')->get();
    }

    /**
     * Get patient by ID.
     */
    public function getPatient($id)
    {
        return Patient::find($id);
    }

    /**
     * Save vital signs for a patient.
     */
    public function saveVitals(array $data)
    {
        try {
            return DB::connection('pgsql_transaction')->transaction(function () use ($data) {
                $vital = Vital::create([
                    'patient_id' => $data['selectedPatientId'],
                    'nurse_id' => Auth::id(),
                    'recorded_at' => now(),
                    'temperature_celsius' => $data['temperature'],
                    'blood_pressure_systolic' => (int) explode('/', $data['bloodPressure'])[0],
                    'blood_pressure_diastolic' => (int) explode('/', $data['bloodPressure'])[1],
                    'heart_rate_bpm' => $data['heartRate'],
                    'spo2_percentage' => $data['oxygenSaturation'],
                    'respiratory_rate' => $data['respiratoryRate'],
                    'weight_kg' => $data['weightKg'],
                    'height_cm' => $data['heightCm'],
                    'bmi' => $this->calculateBMI($data['weightKg'], $data['heightCm']),
                    'flag_abnormal' => $data['flagAbnormal'],
                    'notes' => $data['nurseNotes'],
                ]);

                return $vital;
            });
        } catch (\Exception $e) {
            Log::error('Error saving vitals: ' . $e->getMessage(), [
                'patient_id' => $data['selectedPatientId'],
                'user_id' => Auth::id()
            ]);
            throw $e;
        }
    }

    /**
     * Calculate BMI.
     */
    private function calculateBMI($weightKg, $heightCm)
    {
        if ($weightKg && $heightCm && $heightCm > 0) {
            $heightInMeters = $heightCm / 100;
            return round($weightKg / ($heightInMeters * $heightInMeters), 2);
        }
        return null;
    }

    /**
     * Search patients.
     */
    public function searchPatients($search)
    {
        $allPatients = Patient::all();
        return $allPatients->filter(function ($patient) use ($search) {
            return str_contains(strtolower($patient->name), strtolower($search)) ||
                   str_contains($patient->patient_uid, $search);
        })->take(10);
    }

    /**
     * Save a care report.
     */
    public function saveCareReport(array $data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $report = NurseCareReport::create([
                    'patient_id' => $data['patient_id'],
                    'user_id' => Auth::id(),
                    'report_time' => $data['report_time'],
                    'shift_type' => $data['shift_type'],
                    'interventions' => $data['interventions'],
                    'observations' => $data['observations'],
                ]);

                if (!empty($data['vitals_bp']) || !empty($data['vitals_hr']) ||
                    !empty($data['vitals_temp']) || !empty($data['vitals_spo2'])) {
                    Vital::create([
                        'patient_id' => $data['patient_id'],
                        'user_id' => Auth::id(),
                        'blood_pressure' => $data['vitals_bp'],
                        'heart_rate' => $data['vitals_hr'],
                        'temperature' => $data['vitals_temp'],
                        'oxygen_saturation' => $data['vitals_spo2'],
                        'recorded_at' => $data['report_time'],
                    ]);
                }

                return $report;
            });
        } catch (\Exception $e) {
            Log::error('Error saving care report: ' . $e->getMessage(), [
                'patient_id' => $data['patient_id'],
                'user_id' => Auth::id()
            ]);
            throw $e;
        }
    }
}
