<?php

namespace Database\Factories;

use App\Models\DemoRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DemoRequest>
 */
class DemoRequestFactory extends Factory
{
    protected $model = DemoRequest::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'company' => fake()->optional(0.7)->randomElement([
                'City General Hospital',
                'Green Valley Clinic',
                'Lagos Teaching Hospital',
                'Abuja Medical Center',
                'Enugu Specialist Hospital',
                'Port Harcourt General Hospital',
                'Ibadan Healthcare Center',
                'Kano Regional Hospital',
            ]),
            'message' => fake()->optional(0.6)->paragraph(),
            'status' => 'new',
        ];
    }
}
