<?php

namespace Database\Seeders;

use App\Models\Dispensation;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Database\Seeder;

class DispensationSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::all();
        $prescriptions = Prescription::all();
        $pharmacists = User::role('pharma')->get();

        if ($patients->isEmpty()) {
            return;
        }

        for ($i = 0; $i < 10; $i++) {
            Dispensation::create([
                'patient_id' => $patients->random()->id,
                'prescription_id' => $prescriptions->isNotEmpty() ? $prescriptions->random()->id : null,
                'dispensed_by' => $pharmacists->isNotEmpty() ? $pharmacists->random()->id : User::factory(),
                'dispensed_at' => now()->subDays(rand(1, 30)),
                'total_amount' => rand(200, 5000),
                'notes' => rand(0, 1) ? fake()->sentence() : null,
            ]);
        }
    }
}
