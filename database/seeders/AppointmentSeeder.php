<?php

namespace Database\Seeders;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::all();
        $doctors = User::role('doctor')->get();

        if ($patients->isEmpty()) {
            return;
        }

        $statuses = AppointmentStatus::cases();

        for ($i = 0; $i < 20; $i++) {
            Appointment::create([
                'patient_id' => $patients->random()->id,
                'doctor_id' => $doctors->isNotEmpty() ? $doctors->random()->id : null,
                'appointment_date' => now()->addDays(rand(-30, 60))->format('Y-m-d'),
                'appointment_time' => sprintf('%02d:%02d:00', rand(8, 16), rand(0, 3) * 15),
                'status' => $statuses[array_rand($statuses)]->value,
                'reason_for_visit' => fake()->randomElement(['Routine checkup', 'Follow-up', 'New complaint', 'Lab review']),
                'consultation_fee' => rand(2000, 15000),
                'queue_position' => rand(1, 15),
            ]);
        }
    }
}
