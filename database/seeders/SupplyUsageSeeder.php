<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\Supply;
use App\Models\SupplyUsage;
use App\Models\User;
use Illuminate\Database\Seeder;

class SupplyUsageSeeder extends Seeder
{
    public function run(): void
    {
        $supplies = Supply::all();
        $patients = Patient::all();
        $nurses = User::all();

        if ($supplies->isEmpty() || $patients->isEmpty()) {
            return;
        }

        for ($i = 0; $i < 15; $i++) {
            SupplyUsage::create([
                'supply_id' => $supplies->random()->id,
                'used_by' => $nurses->isNotEmpty() ? $nurses->random()->id : User::factory(),
                'patient_id' => $patients->random()->id,
                'used_quantity' => rand(1, 10),
                'used_at' => now()->subDays(rand(1, 30)),
                'purpose' => fake()->randomElement(['Routine patient care', 'Surgical procedure', 'Wound dressing', 'IV cannulation', 'Blood sample collection']),
            ]);
        }
    }
}
