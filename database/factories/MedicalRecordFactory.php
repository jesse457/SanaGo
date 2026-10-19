<?php

namespace Database\Factories;

use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicalRecord>
 */
class MedicalRecordFactory extends Factory
{
    protected $model = MedicalRecord::class;

    private const DIAGNOSES = [
        'Malaria' => ['Fever, chills, headache, body weakness for 3 days', 'Patient febrile (38.5°C), spleen palpable', 'Artemether/Lumefantrine 20/120mg BD x 3 days. Paracetamol 500mg TDS PRN. Review in 1 week.'],
        'Hypertension' => ['Routine checkup, no acute complaints', 'BP 160/95 mmHg, HR 72 bpm, regular', 'Lisinopril 10mg OD. Dietary counseling. Salt restriction. Monthly BP monitoring.'],
        'Typhoid Fever' => ['High fever, abdominal pain, constipation for 5 days', 'Temp 39.2°C, abdominal tenderness RLQ, coated tongue', 'Ciprofloxacin 500mg BD x 10 days. Paracetamol 500mg TDS. Hydration. Widal test ordered.'],
        'Diabetes Mellitus Type 2' => ['Polyuria, polydipsia, fatigue, weight loss over 3 months', 'FBS 180 mg/dL, BMI 31, acanthosis nigricans', 'Metformin 500mg BD. Diabetic diet counseling. Exercise regimen. FBS monitoring weekly.'],
        'Upper Respiratory Tract Infection' => ['Cough, sore throat, nasal congestion for 4 days', 'Pharynx congested, no lymphadenopathy, chest clear', 'Amoxicillin 500mg TDS x 7 days. Steam inhalation. Warm saline gargles. Adequate fluids.'],
        'Gastroenteritis' => ['Watery diarrhea, vomiting, abdominal cramps for 2 days', 'Mild dehydration, abdomen soft with hyperactive bowel sounds', 'ORS 1L/day. Metronidazole 400mg TDS. Avoid dairy. BRAT diet for 48hrs.'],
        'Urinary Tract Infection' => ['Dysuria, frequency, suprapubic pain for 2 days', 'Suprapubic tenderness, no flank pain, afebrile', 'Ciprofloxacin 500mg BD x 5 days. Increase fluid intake. Cranberry juice. Urinalysis ordered.'],
        'Peptic Ulcer Disease' => ['Epigastric pain, worse at night, relieved by food', 'Epigastric tenderness, no guarding or rebound', 'Omeprazole 20mg BD. Triple therapy if H.pylori positive. Avoid NSAIDs and spicy food.'],
    ];

    public function definition(): array
    {
        $diagnosis = fake()->randomElement(array_keys(self::DIAGNOSES));
        [$complaint, $exam, $plan] = self::DIAGNOSES[$diagnosis];

        return [
            'patient_id' => Patient::factory(),
            'doctor_id' => User::factory(),
            'consultation_id' => null,
            'record_date' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'chief_complaint' => $complaint,
            'history_of_present_illness' => 'Patient reports gradual onset. No previous similar episodes. No known aggravating factors.',
            'physical_exam' => $exam,
            'diagnosis' => $diagnosis,
            'treatment_plan' => $plan,
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }
}
