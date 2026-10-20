<?php

namespace Database\Factories;

use App\Enums\PlanFeature;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        $name = ucfirst(fake()->unique()->words(2, true));
        $slug = str($name)->slug()->value();

        return [
            'name' => $name,
            'slug' => $slug,
            'description' => fake()->sentence(),
            'monthly_price' => 15000,
            'yearly_price' => 150000,
            'currency' => 'XAF',
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 1,
            'max_users_per_tenant' => 10,
            'max_patients_per_tenant' => 500,
            'storage_gb' => 5,
            'trial_days' => 14,
            'grace_days' => 3,
            'api_rate_limit' => 60,
            'features_json' => [
                ['feature_label' => 'Up to 10 users', 'is_highlighted' => false],
            ],
            'feature_flags_json' => array_fill_keys(PlanFeature::core(), true),
        ];
    }

    public function basic(): static
    {
        return $this->state(fn () => [
            'slug' => 'basic',
            'name' => 'Basic',
            'sort_order' => 1,
            'monthly_price' => 15000,
            'yearly_price' => 150000,
            'max_users_per_tenant' => 10,
            'max_patients_per_tenant' => 500,
            'feature_flags_json' => PlanFeature::forPlanSlug('basic'),
        ]);
    }

    public function professional(): static
    {
        return $this->state(fn () => [
            'slug' => 'professional',
            'name' => 'Professional',
            'sort_order' => 2,
            'monthly_price' => 35000,
            'yearly_price' => 350000,
            'max_users_per_tenant' => 50,
            'max_patients_per_tenant' => 5000,
            'storage_gb' => 50,
            'feature_flags_json' => PlanFeature::forPlanSlug('professional'),
        ]);
    }

    public function enterprise(): static
    {
        return $this->state(fn () => [
            'slug' => 'enterprise',
            'name' => 'Enterprise',
            'sort_order' => 3,
            'monthly_price' => 90000,
            'yearly_price' => 900000,
            'max_users_per_tenant' => 500,
            'max_patients_per_tenant' => null,
            'storage_gb' => 500,
            'feature_flags_json' => PlanFeature::forPlanSlug('enterprise'),
        ]);
    }
}
