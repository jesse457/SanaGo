<?php

namespace Database\Seeders;

use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class MedicalRecordSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::all();
        $doctors = User::role('doctor')->get();

        if ($patients->isEmpty()) {
            return;
        }

        $diagnoses = [
            'Malaria' => ['Fever, chills, headache for 3 days', 'Febrile 38.5°C, spleen palpable', 'Artemether/Lumefantrine BD x 3 days'],
            'Hypertension' => ['Routine checkup', 'BP 160/95, HR 72', 'Lisinopril 10mg OD, salt restriction'],
            'Typhoid Fever' => ['High fever, abdominal pain for 5 days', 'Temp 39.2°C, RLQ tenderness', 'Ciprofloxacin 500mg BD x 10 days'],
            'Diabetes Type 2' => ['Polyuria, polydipsia, fatigue', 'FBS 180 mg/dL, BMI 31', 'Metformin 500mg BD, diabetic diet'],
            'URTI' => ['Cough, sore throat for 4 days', 'Pharynx congested, chest clear', 'Amoxicillin 500mg TDS x 7 days'],
        ];

        foreach ($diagnoses as $diagnosis => [$complaint, $exam, $plan]) {
            MedicalRecord::create([
                'patient_id' => $patients->random()->id,
                'doctor_id' => $doctors->isNotEmpty() ? $doctors->random()->id : null,
                'record_date' => now()->subDays(rand(1, 90))->format('Y-m-d'),
                'chief_complaint' => $complaint,
                'diagnosis' => $diagnosis,
                'treatment_plan' => $plan,
                'physical_exam' => $exam,
                'history_of_present_illness' => 'Gradual onset. No previous similar episodes.',
                'notes' => rand(0, 1) ? fake()->sentence() : null,
            ]);
        }

        // Add a few more random ones
        for ($i = 0; $i < 10; $i++) {
            $d = array_rand($diagnoses);
            MedicalRecord::create([
                'patient_id' => $patients->random()->id,
                'doctor_id' => $doctors->isNotEmpty() ? $doctors->random()->id : null,
                'record_date' => now()->subDays(rand(1, 90))->format('Y-m-d'),
                'chief_complaint' => $diagnoses[$d][0],
                'diagnosis' => $d,
                'treatment_plan' => $diagnoses[$d][2],
                'physical_exam' => $diagnoses[$d][1],
                'history_of_present_illness' => 'Symptoms have been present for several days.',
            ]);
        }
    }
}
