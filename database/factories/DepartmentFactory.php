<?php

namespace Database\Factories;

use App\Enums\DepartmentType;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'General Surgery',
                'Internal Medicine',
                'Pediatrics',
                'Obstetrics & Gynecology',
                'Orthopedics',
                'Radiology',
                'Anesthesiology',
                'Dermatology',
                'Ophthalmology',
                'ENT',
                'Neurology',
                'Cardiology',
                'Urology',
                'Psychiatry',
                'Emergency Medicine',
                'Pathology',
                'Oncology',
                'Nephrology',
            ]),
            'code' => fn (array $attributes) => strtoupper(substr($attributes['name'], 0, 4)),
            'description' => fn (array $attributes) => "Department of {$attributes['name']} — providing comprehensive healthcare services.",
            'phone_extension' => fake()->numerify('###'),
            'type' => fake()->randomElement(DepartmentType::cases()),
            'is_active' => fake()->boolean(90),
            'head_of_department_id' => null,
        ];
    }

    public function clinical(): static
    {
        return $this->state(fn () => ['type' => DepartmentType::CLINICAL]);
    }

    public function administrative(): static
    {
        return $this->state(fn () => ['type' => DepartmentType::ADMINISTRATIVE]);
    }
}
