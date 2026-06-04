<?php

namespace Database\Factories;

use App\Enums\LabRequestStatus;
use App\Enums\UrgencyLevel;
use App\Models\LabRequest;
use App\Models\LabTestDefinition;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LabRequest>
 */
class LabRequestFactory extends Factory
{
    protected $model = LabRequest::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'test_definition_id' => LabTestDefinition::factory(),
            'doctor_id' => User::factory(),
            'consultation_id' => null,
            'request_date' => fake()->dateTimeBetween('-2 weeks', 'now'),
            'status' => fake()->randomElement(LabRequestStatus::cases()),
            'reason_for_test' => fake()->randomElement([
                'Routine investigation',
                'Diagnostic workup for suspected infection',
                'Pre-operative assessment',
                'Monitoring of chronic condition',
                'Follow-up on previous abnormal result',
                'Annual health screening',
            ]),
            'urgency' => fake()->randomElement(UrgencyLevel::cases()),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => LabRequestStatus::PENDING]);
    }

    public function completed(): static
    {
        return $this->state(fn () => ['status' => LabRequestStatus::COMPLETED]);
    }

    public function urgent(): static
    {
        return $this->state(fn () => ['urgency' => UrgencyLevel::URGENT]);
    }
}
