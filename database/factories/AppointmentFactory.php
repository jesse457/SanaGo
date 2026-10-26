<?php

namespace Database\Factories;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'doctor_id' => User::factory(),
            'appointment_date' => fake()->dateTimeBetween('-1 month', '+2 months')->format('Y-m-d'),
            'appointment_time' => fake()->time('H:i:s'),
            'status' => fake()->randomElement(AppointmentStatus::cases()),
            'consultation_fee' => fake()->randomFloat(2, 2000, 15000),
        ];
    }

    public function scheduled(): static
    {
        return $this->state(fn () => [
            'status' => AppointmentStatus::SCHEDULED,
            'appointment_date' => fake()->dateTimeBetween('now', '+2 months')->format('Y-m-d'),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => AppointmentStatus::COMPLETED,
            'appointment_date' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => ['status' => AppointmentStatus::CANCELLED]);
    }
}
