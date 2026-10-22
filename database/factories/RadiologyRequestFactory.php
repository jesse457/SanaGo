<?php

namespace Database\Factories;

use App\Enums\RadiologyStatus;
use App\Models\Patient;
use App\Models\RadiologyRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RadiologyRequest>
 */
class RadiologyRequestFactory extends Factory
{
    protected $model = RadiologyRequest::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'consultation_id' => null,
            'requested_by' => User::factory(),
            'imaging_type' => fake()->randomElement([
                'x_ray', 'ultrasound', 'ct_scan', 'mri', 'mammography',
            ]),
            'body_part' => fake()->randomElement([
                'chest', 'abdomen', 'pelvis', 'head', 'spine',
                'extremity_upper', 'extremity_lower',
            ]),
            'clinical_notes' => fake()->optional(0.7)->randomElement([
                'Persistent cough for three weeks, rule out consolidation.',
                'Blunt trauma to the right ankle after a fall.',
                'Recurrent epigastric pain, assess for gallstones.',
                'Suspected stress fracture of the metatarsals.',
                'Routine pre-operative chest film.',
            ]),
            'status' => fake()->randomElement(RadiologyStatus::cases()),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => RadiologyStatus::Pending]);
    }

    public function inProgress(): static
    {
        return $this->state(fn () => ['status' => RadiologyStatus::InProgress]);
    }

    public function completed(): static
    {
        return $this->state(fn () => ['status' => RadiologyStatus::Completed]);
    }

    public function validated(): static
    {
        return $this->state(fn () => ['status' => RadiologyStatus::Validated]);
    }
}
