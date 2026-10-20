<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\PlanFeature;
use App\Models\Plan;
use App\Models\Subscription;
use App\Support\PlatformContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Plan catalogue management.
 *
 * Plans carry both marketing bullets (`features_json`) and the capability map
 * (`feature_flags_json`) the application actually enforces, plus quotas.
 */
class PlanController extends Controller
{
    public function index(): Response
    {
        $plans = Plan::query()
            ->withCount('subscriptions')
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Plan $plan) => [
                'id' => $plan->id,
                'name' => $plan->name,
                'slug' => $plan->slug,
                'description' => $plan->description,
                'monthly_price' => (float) $plan->monthly_price,
                'yearly_price' => (float) $plan->yearly_price,
                'currency' => $plan->currency,
                'is_active' => $plan->is_active,
                'is_featured' => $plan->is_featured,
                'sort_order' => $plan->sort_order,
                'max_users_per_tenant' => $plan->max_users_per_tenant,
                'max_patients_per_tenant' => $plan->max_patients_per_tenant,
                'storage_gb' => $plan->storage_gb,
                'trial_days' => $plan->trial_days,
                'api_rate_limit' => $plan->api_rate_limit,
                'features_json' => $plan->features_json ?? [],
                'feature_flags' => $plan->featureFlags(),
                'subscriptions_count' => $plan->subscriptions_count,
            ]);

        return Inertia::render('Plans/Index', [
            'plans' => $plans,
            'featureOptions' => PlanFeature::options(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Plans/Create', [
            'featureOptions' => PlanFeature::options(),
            'defaults' => [
                'currency' => PlatformContext::currency(),
                'feature_flags' => PlanFeature::forPlanSlug('basic'),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePlan($request);

        Plan::create($validated);

        return redirect()->route('plans.index')->with('success', 'Plan created.');
    }

    public function edit(Plan $plan): Response
    {
        return Inertia::render('Plans/Edit', [
            'plan' => [
                'id' => $plan->id,
                'name' => $plan->name,
                'slug' => $plan->slug,
                'description' => $plan->description,
                'monthly_price' => (float) $plan->monthly_price,
                'yearly_price' => (float) $plan->yearly_price,
                'currency' => $plan->currency,
                'is_active' => $plan->is_active,
                'is_featured' => $plan->is_featured,
                'sort_order' => $plan->sort_order,
                'max_users_per_tenant' => $plan->max_users_per_tenant,
                'max_patients_per_tenant' => $plan->max_patients_per_tenant,
                'storage_gb' => $plan->storage_gb,
                'trial_days' => $plan->trial_days,
                'grace_days' => $plan->grace_days,
                'api_rate_limit' => $plan->api_rate_limit,
                'features_json' => $plan->features_json ?? [],
                'feature_flags' => $plan->featureFlags(),
            ],
            'featureOptions' => PlanFeature::options(),
            'subscribers' => Subscription::query()->where('plan_id', $plan->id)->count(),
        ]);
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $validated = $this->validatePlan($request, $plan);

        $plan->update($validated);

        return redirect()->route('plans.index')->with('success', 'Plan updated.');
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        if (Subscription::query()->where('plan_id', $plan->id)->exists()) {
            return redirect()->back()->with(
                'error',
                'This plan still has active subscribers. Move them to another plan first.',
            );
        }

        $plan->delete();

        return redirect()->route('plans.index')->with('success', 'Plan deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePlan(Request $request, ?Plan $plan = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:plans,slug,'.($plan?->id ?? 'NULL')],
            'description' => ['nullable', 'string'],
            'monthly_price' => ['nullable', 'numeric', 'min:0'],
            'yearly_price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'max_users_per_tenant' => ['nullable', 'integer', 'min:1'],
            'max_patients_per_tenant' => ['nullable', 'integer', 'min:1'],
            'storage_gb' => ['nullable', 'integer', 'min:0'],
            'trial_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'grace_days' => ['nullable', 'integer', 'min:0', 'max:90'],
            'api_rate_limit' => ['nullable', 'integer', 'min:10', 'max:10000'],
            'features_json' => ['nullable', 'array'],
            'features_json.*.feature_label' => ['required_with:features_json', 'string', 'max:120'],
            'features_json.*.is_highlighted' => ['boolean'],
            'feature_flags' => ['nullable', 'array'],
        ]);

        $validated['feature_flags_json'] = collect($request->input('feature_flags', []))
            ->filter(fn ($enabled) => filter_var($enabled, FILTER_VALIDATE_BOOLEAN))
            ->keys()
            ->mapWithKeys(fn ($key) => [$key => true])
            ->all();

        unset($validated['feature_flags']);
        $validated['currency'] = $validated['currency'] ?? PlatformContext::currency();

        return $validated;
    }
}
