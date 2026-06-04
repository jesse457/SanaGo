<?php

namespace Database\Factories;

use App\Enums\AdmissionStatus;
use App\Models\Admission;
use App\Models\Bed;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Admission>
 */
class AdmissionFactory extends Factory
{
    protected $model = Admission::class;

    public function definition(): array
    {
        $admissionDate = fake()->dateTimeBetween('-30 days', 'now');

        return [
            'patient_id' => Patient::factory(),
            'bed_id' => Bed::factory(),
            'attending_doctor_id' => User::factory(),
            'admission_date' => $admissionDate->format('Y-m-d'),
            'discharge_date' => null,
            'status' => AdmissionStatus::Admitted,
            'reason_for_admission' => fake()->randomElement([
                'Severe malaria requiring IV therapy',
                'Acute gastroenteritis with dehydration',
                'Hypertensive urgency for monitoring',
                'Post-operative observation',
                'Pneumonia requiring IV antibiotics',
                'Diabetic ketoacidosis management',
                'Acute appendicitis — pre-operative',
                'Congestive heart failure exacerbation',
            ]),
            'observation_fee' => fake()->randomFloat(2, 500, 3000),
        ];
    }

    public function admitted(): static
    {
        return $this->state(fn () => [
            'status' => AdmissionStatus::Admitted,
            'discharge_date' => null,
        ]);
    }

    public function discharged(): static
    {
        return $this->state(fn () => [
            'status' => AdmissionStatus::Discharged,
            'discharge_date' => fake()->dateTimeBetween('+2 days', '+21 days')->format('Y-m-d'),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => AdmissionStatus::Pending,
            'discharge_date' => null,
        ]);
    }
}
