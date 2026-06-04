<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Supply;
use App\Models\SupplyUsage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupplyUsage>
 */
class SupplyUsageFactory extends Factory
{
    protected $model = SupplyUsage::class;

    public function definition(): array
    {
        return [
            'supply_id' => Supply::factory(),
            'used_by' => User::factory(),
            'patient_id' => Patient::factory(),
            'used_quantity' => fake()->numberBetween(1, 10),
            'used_at' => fake()->dateTimeBetween('-3 months', 'now'),
            'purpose' => fake()->randomElement([
                'Routine patient care',
                'Surgical procedure',
                'Wound dressing',
                'IV cannulation',
                'Blood sample collection',
                'Emergency intervention',
                'Catheterization',
                'Diagnostic procedure',
            ]),
        ];
    }
}
