<?php

namespace Database\Factories;

use App\Enums\PrescriptionStatus;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prescription>
 */
class PrescriptionFactory extends Factory
{
    protected $model = Prescription::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'doctor_id' => User::factory(),
            'prescription_date' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'status' => fake()->randomElement(PrescriptionStatus::cases()),
            'notes' => fake()->optional(0.5)->sentence(),
            'total_amount' => 0, // Will be calculated from items
        ];
    }

    public function new(): static
    {
        return $this->state(fn () => ['status' => PrescriptionStatus::NEW]);
    }

    public function completed(): static
    {
        return $this->state(fn () => ['status' => PrescriptionStatus::COMPLETED]);
    }
}
