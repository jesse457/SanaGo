<?php

namespace Database\Seeders;

use App\Enums\PlanFeature;
use App\Enums\SubscriptionStatus;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

/**
 * Demo plan catalogue + one subscription per existing tenant.
 *
 * Prices are in XAF (FCFA), which is what Cameroonian hospitals are billed
 * in, and every plan carries a real capability map so the tenant app can gate
 * modules. Idempotent: re-running updates the plan definitions in place.
 */
class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'For a single clinic getting its registration, appointments and pharmacy onto one system.',
                'monthly_price' => 15000,
                'yearly_price' => 150000,
                'sort_order' => 1,
                'is_featured' => false,
                'max_users_per_tenant' => 10,
                'max_patients_per_tenant' => 2000,
                'storage_gb' => 5,
                'trial_days' => 14,
                'grace_days' => 3,
                'api_rate_limit' => 60,
                'features_json' => [
                    ['feature_label' => 'Up to 10 users', 'is_highlighted' => false],
                    ['feature_label' => '2 000 patient records', 'is_highlighted' => false],
                    ['feature_label' => 'Appointments & consultations', 'is_highlighted' => true],
                    ['feature_label' => 'Pharmacy & inventory', 'is_highlighted' => true],
                    ['feature_label' => 'Email support', 'is_highlighted' => false],
                ],
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'The full hospital floor: diagnostics, surgery, cash register and insurance claims.',
                'monthly_price' => 45000,
                'yearly_price' => 450000,
                'sort_order' => 2,
                'is_featured' => true,
                'max_users_per_tenant' => 50,
                'max_patients_per_tenant' => 20000,
                'storage_gb' => 50,
                'trial_days' => 14,
                'grace_days' => 5,
                'api_rate_limit' => 180,
                'features_json' => [
                    ['feature_label' => 'Up to 50 users', 'is_highlighted' => false],
                    ['feature_label' => '20 000 patient records', 'is_highlighted' => false],
                    ['feature_label' => 'Laboratory & radiology', 'is_highlighted' => true],
                    ['feature_label' => 'Surgery & emergency', 'is_highlighted' => true],
                    ['feature_label' => 'Cash register (Caisse)', 'is_highlighted' => true],
                    ['feature_label' => 'Tiers payant & claims', 'is_highlighted' => true],
                    ['feature_label' => 'SMS notifications', 'is_highlighted' => true],
                    ['feature_label' => 'Priority support', 'is_highlighted' => true],
                ],
            ],
            [
                'name' => 'Hospital',
                'slug' => 'hospital',
                'description' => 'The whole system for one hospital: every module and custom branding, without API access or SMS credit.',
                'monthly_price' => 75000,
                'yearly_price' => 750000,
                'sort_order' => 3,
                'is_featured' => false,
                'max_users_per_tenant' => 150,
                'max_patients_per_tenant' => 100000,
                'storage_gb' => 150,
                'trial_days' => 14,
                'grace_days' => 7,
                'api_rate_limit' => 120,
                'features_json' => [
                    ['feature_label' => 'Up to 150 users', 'is_highlighted' => false],
                    ['feature_label' => '100 000 patient records', 'is_highlighted' => false],
                    ['feature_label' => 'Every clinical module', 'is_highlighted' => true],
                    ['feature_label' => 'Laboratory, radiology & imaging', 'is_highlighted' => true],
                    ['feature_label' => 'Surgery, emergency & admissions', 'is_highlighted' => true],
                    ['feature_label' => 'Cash register & insurance claims', 'is_highlighted' => true],
                    ['feature_label' => 'Custom branding', 'is_highlighted' => false],
                    ['feature_label' => 'No API access or SMS credit', 'is_highlighted' => false],
                ],
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'For hospital groups: unlimited records, API access, custom branding and a named account manager.',
                'monthly_price' => 120000,
                'yearly_price' => 1200000,
                'sort_order' => 4,
                'is_featured' => false,
                'max_users_per_tenant' => 500,
                'max_patients_per_tenant' => null,
                'storage_gb' => 500,
                'trial_days' => 30,
                'grace_days' => 7,
                'api_rate_limit' => 600,
                'features_json' => [
                    ['feature_label' => 'Up to 500 users', 'is_highlighted' => false],
                    ['feature_label' => 'Unlimited patient records', 'is_highlighted' => true],
                    ['feature_label' => 'API access', 'is_highlighted' => true],
                    ['feature_label' => 'Custom branding', 'is_highlighted' => true],
                    ['feature_label' => '500 GB storage', 'is_highlighted' => false],
                    ['feature_label' => 'Dedicated support', 'is_highlighted' => true],
                ],
            ],
        ];

        foreach ($plans as $planData) {
            Plan::query()->updateOrCreate(
                ['slug' => $planData['slug']],
                [
                    ...$planData,
                    'currency' => 'XAF',
                    'is_active' => true,
                    'feature_flags_json' => PlanFeature::forPlanSlug($planData['slug']),
                ],
            );
        }

        $tenants = Tenant::query()->get();

        if ($tenants->isEmpty()) {
            return;
        }

        $planModels = Plan::query()->orderBy('sort_order')->get();

        $tenants->values()->each(function (Tenant $tenant, int $index) use ($planModels) {
            $plan = $planModels[$index % max(1, $planModels->count())] ?? $planModels->first();

            if ($plan === null) {
                return;
            }

            $start = now()->startOfDay();
            $cycle = $plan->monthly_price !== null ? 'monthly' : 'yearly';

            Subscription::query()->updateOrCreate(
                ['tenant_id' => $tenant->getTenantKey()],
                [
                    'plan_id' => $plan->id,
                    'plan_name' => $plan->name,
                    'billing_cycle' => $cycle,
                    'currency' => $plan->currency,
                    'price' => $plan->priceFor($cycle),
                    'start_date' => $start,
                    'end_date' => $cycle === 'yearly' ? $start->copy()->addYear()->subDay() : $start->copy()->addMonth()->subDay(),
                    'status' => SubscriptionStatus::ACTIVE,
                    'auto_renew' => true,
                    'cancel_at_period_end' => false,
                    'features_json' => $plan->features_json,
                ],
            );
        });
    }
}
