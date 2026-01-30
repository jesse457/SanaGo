<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => Str::uuid(),
            'notifiable_id' => User::factory(),
            'notifiable_type' => 'App\Models\User',
            'type' => $this->faker->randomElement(['lab_order', 'pharmacy_order', 'patient_admission', 'appointment_reminder', 'general']),
            'data' => [
                'id' => $this->faker->uuid(),
                'message' => $this->faker->sentence(),
                'patient_name' => $this->faker->name(),
                'doctor_name' => $this->faker->name(),
                'consultation_id' => $this->faker->randomNumber(),
                'prescription_id' => $this->faker->randomNumber(),
                'admission_id' => $this->faker->randomNumber(),
                'appointment_id' => $this->faker->randomNumber(),
                'appointment_time' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
                'ward_name' => $this->faker->word(),
                'bed_number' => $this->faker->randomNumber(),
                'urgency' => $this->faker->randomElement(['low', 'normal', 'high', 'urgent']),
                'type' => $this->faker->randomElement(['lab_order', 'pharmacy_order', 'patient_admission', 'appointment_reminder', 'general']),
                'created_at' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
            ],
            'read_at' => $this->faker->optional()->dateTime(),
        ];
    }

    /**
     * Indicate that the notification is unread.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unread()
    {
        return $this->state(function (array $attributes) {
            return [
                'read_at' => null,
            ];
        });
    }

    /**
     * Indicate that the notification is read.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function read()
    {
        return $this->state(function (array $attributes) {
            return [
                'read_at' => $this->faker->dateTime(),
            ];
        });
    }

    /**
     * Indicate that the notification is of a specific type.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function type(string $type)
    {
        return $this->state(function (array $attributes) use ($type) {
            return [
                'type' => $type,
                'data' => array_merge($attributes['data'], ['type' => $type]),
            ];
        });
    }
}
