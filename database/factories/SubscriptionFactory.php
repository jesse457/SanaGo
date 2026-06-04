<?php

namespace Database\Factories;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'plan_name' => fake()->randomElement(['Basic', 'Professional', 'Enterprise']),
            'billing_cycle' => fake()->randomElement(['monthly', 'yearly']),
            'price' => fake()->randomFloat(2, 49, 999),
            'start_date' => fake()->date(),
            'end_date' => fake()->optional()->date(),
            'status' => fake()->randomElement(SubscriptionStatus::cases()),
            'features_json' => ['max_users' => fake()->numberBetween(5, 100), 'storage_gb' => fake()->numberBetween(1, 500)],
        ];
    }
}
