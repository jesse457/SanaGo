<?php

namespace Database\Seeders;

use App\Enums\PrescriptionStatus;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Database\Seeder;

class PrescriptionSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::all();
        $doctors = User::role('doctor')->get();

        if ($patients->isEmpty()) {
            return;
        }

        $statuses = PrescriptionStatus::cases();

        for ($i = 0; $i < 12; $i++) {
            Prescription::create([
                'patient_id' => $patients->random()->id,
                'doctor_id' => $doctors->isNotEmpty() ? $doctors->random()->id : null,
                'prescription_date' => now()->subDays(rand(1, 60))->format('Y-m-d'),
                'status' => $statuses[array_rand($statuses)]->value,
                'notes' => rand(0, 1) ? fake()->sentence() : null,
                'total_amount' => 0,
            ]);
        }
    }
}
