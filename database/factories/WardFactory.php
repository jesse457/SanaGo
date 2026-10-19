<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Ward;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ward>
 */
class WardFactory extends Factory
{
    protected $model = Ward::class;

    private const WARDS = [
        'General Ward',
        'Intensive Care Unit',
        'Maternity Ward',
        'Pediatric Ward',
        'Surgical Ward',
        'Isolation Ward',
        'Recovery Ward',
        'Emergency Ward',
    ];

    public function definition(): array
    {
        $name = fake()->randomElement(self::WARDS);

        return [
            'name' => $name,
            'code' => strtoupper(substr($name, 0, 4)).'-'.fake()->randomNumber(3, true),
            'description' => "{$name} — providing quality inpatient care.",
            'department_id' => Department::factory(),
            'floor' => fake()->randomElement(['Ground', '1st', '2nd', '3rd', '4th']),
            'capacity' => fake()->numberBetween(8, 40),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
