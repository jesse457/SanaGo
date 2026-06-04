<?php

namespace Database\Factories;

use App\Models\Dispensation;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dispensation>
 */
class DispensationFactory extends Factory
{
    protected $model = Dispensation::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'prescription_id' => Prescription::factory(),
            'dispensed_by' => User::factory(),
            'dispensed_at' => fake()->dateTimeBetween('-2 months', 'now'),
            'total_amount' => fake()->randomFloat(2, 100, 5000),
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }
}
