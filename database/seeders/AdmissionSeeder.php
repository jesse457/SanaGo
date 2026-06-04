<?php

namespace Database\Seeders;

use App\Models\Admission;
use App\Models\Bed;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdmissionSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::all();
        $beds = Bed::all();
        $doctors = User::role('doctor')->get();

        if ($patients->isEmpty() || $beds->isEmpty()) {
            return;
        }

        $reasons = [
            'Severe malaria requiring IV therapy',
            'Acute gastroenteritis with dehydration',
            'Hypertensive urgency for monitoring',
            'Post-operative observation',
            'Pneumonia requiring IV antibiotics',
            'Diabetic ketoacidosis management',
        ];

        for ($i = 0; $i < 12; $i++) {
            Admission::create([
                'patient_id' => $patients->random()->id,
                'bed_id' => $beds->random()->id,
                'attending_doctor_id' => $doctors->isNotEmpty() ? $doctors->random()->id : null,
                'admission_date' => now()->subDays(rand(1, 30))->format('Y-m-d'),
                'status' => rand(0, 3) === 0 ? 'discharged' : 'admitted',
                'discharge_date' => rand(0, 3) === 0 ? now()->subDays(rand(0, 5))->format('Y-m-d') : null,
                'reason_for_admission' => $reasons[array_rand($reasons)],
                'observation_fee' => rand(500, 3000),
            ]);
        }
    }
}
