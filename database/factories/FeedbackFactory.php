<?php

namespace Database\Factories;

use App\Enums\FeedbackStatus;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Feedback>
 */
class FeedbackFactory extends Factory
{
    protected $model = Feedback::class;

    public function definition(): array
    {
        return [
            'subject' => fake()->randomElement([
                'Excellent care at the clinic',
                'Long waiting time at reception',
                'Very helpful nursing staff',
                'Facility cleanliness commendation',
                'Suggestion for pharmacy hours',
                'Grateful for emergency care',
                'Appointment scheduling difficulty',
                'Positive experience with lab services',
            ]),
            'message' => fake()->paragraph(),
            'category' => fake()->randomElement(['Service', 'Facilities', 'Staff', 'Billing', 'Pharmacy', 'Laboratory', 'Other']),
            'status' => fake()->randomElement(FeedbackStatus::cases()),
            'response' => null,
            'responder_id' => null,
            'user_id' => User::factory(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => FeedbackStatus::PENDING,
            'response' => null,
            'responder_id' => null,
        ]);
    }

    public function addressed(): static
    {
        return $this->state(fn () => [
            'status' => FeedbackStatus::ADDRESSED,
            'response' => fake()->paragraph(),
            'responder_id' => User::factory(),
        ]);
    }
}
