<?php

namespace Database\Seeders;

use App\Models\LabRequest;
use App\Models\LabTestDefinition;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class LabRequestSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::all();
        $tests = LabTestDefinition::all();
        $doctors = User::role('doctor')->get();

        if ($patients->isEmpty() || $tests->isEmpty()) {
            return;
        }

        for ($i = 0; $i < 12; $i++) {
            LabRequest::create([
                'patient_id' => $patients->random()->id,
                'test_definition_id' => $tests->random()->id,
                'doctor_id' => $doctors->isNotEmpty() ? $doctors->random()->id : null,
                'request_date' => now()->subDays(rand(1, 14)),
                'status' => fake()->randomElement(['pending', 'in_progress', 'completed']),
                'urgency' => fake()->randomElement(['normal', 'urgent', 'emergency']),
                'reason_for_test' => fake()->randomElement([
                    'Routine investigation', 'Diagnostic workup', 'Pre-operative assessment',
                    'Monitoring of chronic condition', 'Follow-up on abnormal result',
                ]),
            ]);
        }
    }
}
