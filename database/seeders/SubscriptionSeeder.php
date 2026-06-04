<?php

namespace Database\Seeders;

use App\Enums\SubscriptionStatus;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Perfect for small clinics getting started with digital management.',
                'monthly_price' => 99.99,
                'yearly_price' => 999.99,
                'is_active' => true,
                'sort_order' => 1,
                'max_users_per_tenant' => 10,
                'storage_gb' => 5,
                'features_json' => [
                    ['feature_label' => 'Up to 10 Users', 'is_highlighted' => false],
                    ['feature_label' => '5 GB Storage', 'is_highlighted' => false],
                    ['feature_label' => 'Email Support', 'is_highlighted' => false],
                    ['feature_label' => 'Unlimited Patient Records', 'is_highlighted' => true],
                    ['feature_label' => 'Appointment Scheduling', 'is_highlighted' => true],
                    ['feature_label' => 'Basic Lab Management', 'is_highlighted' => false],
                ],
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'Ideal for growing hospitals needing advanced features and priority support.',
                'monthly_price' => 199.99,
                'yearly_price' => 1999.99,
                'is_active' => true,
                'sort_order' => 2,
                'max_users_per_tenant' => 50,
                'storage_gb' => 50,
                'features_json' => [
                    ['feature_label' => 'Up to 50 Users', 'is_highlighted' => false],
                    ['feature_label' => '50 GB Storage', 'is_highlighted' => false],
                    ['feature_label' => 'Priority Support', 'is_highlighted' => true],
                    ['feature_label' => 'Unlimited Patient Records', 'is_highlighted' => true],
                    ['feature_label' => 'Advanced Scheduling', 'is_highlighted' => true],
                    ['feature_label' => 'Full Lab Management', 'is_highlighted' => true],
                    ['feature_label' => 'Pharmacy & Inventory', 'is_highlighted' => false],
                    ['feature_label' => 'Billing & Revenue', 'is_highlighted' => false],
                ],
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'For hospital networks requiring maximum capacity, dedicated support, and custom configurations.',
                'monthly_price' => null,
                'yearly_price' => 4999.99,
                'is_active' => true,
                'sort_order' => 3,
                'max_users_per_tenant' => 999,
                'storage_gb' => 500,
                'features_json' => [
                    ['feature_label' => 'Unlimited Users', 'is_highlighted' => false],
                    ['feature_label' => '500 GB Storage', 'is_highlighted' => false],
                    ['feature_label' => 'Dedicated Support', 'is_highlighted' => true],
                    ['feature_label' => 'Unlimited Patient Records', 'is_highlighted' => true],
                    ['feature_label' => 'Enterprise Scheduling', 'is_highlighted' => true],
                    ['feature_label' => 'Full Lab Management', 'is_highlighted' => true],
                    ['feature_label' => 'Pharmacy & Inventory', 'is_highlighted' => true],
                    ['feature_label' => 'Advanced Billing', 'is_highlighted' => true],
                    ['feature_label' => 'API Access', 'is_highlighted' => true],
                    ['feature_label' => 'Custom Branding', 'is_highlighted' => false],
                ],
            ],
        ];

        foreach ($plans as $planData) {
            Plan::create($planData);
        }

        // ─── Create Subscriptions for Existing Tenants ───
        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            return;
        }

        $planModels = Plan::all();

        $tenants->each(function ($tenant, $i) use ($planModels) {
            $plan = $planModels[$i % count($planModels)];
            $price = $plan->monthly_price ?? $plan->yearly_price;

            Subscription::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'billing_cycle' => $plan->monthly_price ? 'monthly' : 'yearly',
                'price' => $price,
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'status' => SubscriptionStatus::ACTIVE,
                'features_json' => $plan->features_json,
            ]);
        });
    }
}
