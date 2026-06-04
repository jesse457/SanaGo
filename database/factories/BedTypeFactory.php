<?php

namespace Database\Factories;

use App\Models\BedType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BedType>
 */
class BedTypeFactory extends Factory
{
    protected $model = BedType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'General Ward Bed',
                'ICU Bed',
                'HDU Bed',
                'Private Room Bed',
                'Pediatric Cot',
                'Isolation Bed',
                'Maternity Bed',
                'Recovery Bed',
            ]),
            'description' => fn (array $attributes) => "{$attributes['name']} — standard hospital accommodation.",
            'capacity' => fake()->numberBetween(1, 4),
            'daily_rate' => fake()->randomFloat(2, 30, 800),
        ];
    }

    public function icu(): static
    {
        return $this->state(fn () => [
            'name' => 'ICU Bed',
            'daily_rate' => fake()->randomFloat(2, 400, 800),
        ]);
    }

    public function general(): static
    {
        return $this->state(fn () => [
            'name' => 'General Ward Bed',
            'daily_rate' => fake()->randomFloat(2, 30, 100),
        ]);
    }
}
