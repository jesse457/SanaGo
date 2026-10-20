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

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => SubscriptionStatus::ACTIVE,
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(25),
        ]);
    }

    public function trialing(): static
    {
        return $this->state(fn () => [
            'status' => SubscriptionStatus::TRIALING,
            'trial_ends_at' => now()->addDays(7),
            'end_date' => now()->addDays(7),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'status' => SubscriptionStatus::EXPIRED,
            'start_date' => now()->subMonths(2),
            'end_date' => now()->subMonth(),
            'auto_renew' => false,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => SubscriptionStatus::CANCELLED,
            'cancelled_at' => now(),
            'auto_renew' => false,
        ]);
    }

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'plan_name' => fake()->randomElement(['Basic', 'Professional', 'Enterprise']),
            'billing_cycle' => fake()->randomElement(['monthly', 'yearly']),
            'plan_id' => null,
            'price' => 15000,
            'currency' => 'XAF',
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(25),
            'trial_ends_at' => null,
            'status' => SubscriptionStatus::ACTIVE,
            'auto_renew' => true,
            'cancel_at_period_end' => false,
            'features_json' => [['feature_label' => 'Up to 10 users', 'is_highlighted' => false]],
            'limits_json' => null,
            'feature_overrides_json' => null,
        ];
    }
}
