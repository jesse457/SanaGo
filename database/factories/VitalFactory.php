<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\User;
use App\Models\Vital;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vital>
 */
class VitalFactory extends Factory
{
    protected $model = Vital::class;

    public function definition(): array
    {
        $temperature = fake()->randomFloat(1, 36.0, 40.5);
        $systolic = fake()->numberBetween(90, 180);
        $diastolic = fake()->numberBetween(50, 110);
        $heartRate = fake()->numberBetween(55, 130);
        $oxygenSaturation = fake()->numberBetween(85, 100);
        $respiratoryRate = fake()->numberBetween(12, 28);

        $isAbnormal = $temperature > 37.8
            || $systolic > 140 || $diastolic > 90
            || $heartRate > 100 || $heartRate < 60
            || $oxygenSaturation < 95;

        return [
            'patient_id' => Patient::factory(),
            'recorded_by' => User::factory(),
            'recorded_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'temperature' => $temperature,
            'systolic_bp' => $systolic,
            'diastolic_bp' => $diastolic,
            'heart_rate' => $heartRate,
            'oxygen_saturation' => $oxygenSaturation,
            'respiratory_rate' => $respiratoryRate,
            'is_abnormal' => $isAbnormal,
            'notes' => $isAbnormal ? fake()->sentence() : null,
        ];
    }

    public function normal(): static
    {
        return $this->state(fn () => [
            'temperature' => fake()->randomFloat(1, 36.5, 37.2),
            'systolic_bp' => fake()->numberBetween(100, 130),
            'diastolic_bp' => fake()->numberBetween(60, 85),
            'heart_rate' => fake()->numberBetween(60, 90),
            'oxygen_saturation' => fake()->numberBetween(95, 100),
            'is_abnormal' => false,
            'notes' => null,
        ]);
    }

    public function abnormal(): static
    {
        return $this->state(fn () => [
            'temperature' => fake()->randomFloat(1, 38.0, 40.5),
            'heart_rate' => fake()->numberBetween(100, 130),
            'oxygen_saturation' => fake()->numberBetween(85, 94),
            'is_abnormal' => true,
            'notes' => 'Patient requires monitoring.',
        ]);
    }
}
