<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionController extends Controller
{
    public function index(): Response
    {
        $subscriptions = Subscription::with('tenant')
            ->latest()
            ->get()
            ->map(fn ($sub) => [
                'id' => $sub->id,
                'plan_name' => $sub->plan_name,
                'tenant_id' => $sub->tenant_id,
                'tenant_name' => $sub->tenant?->getAttribute('data')['name'] ?? $sub->tenant_id,
                'billing_cycle' => $sub->billing_cycle,
                'price' => (float) $sub->price,
                'start_date' => $sub->start_date?->toISOString(),
                'end_date' => $sub->end_date?->toISOString(),
                'status' => $sub->status->value,
                'features' => $sub->features_json,
                'created_at' => $sub->created_at?->toISOString(),
            ]);

        return Inertia::render('Subscriptions/Index', [
            'subscriptions' => $subscriptions,
        ]);
    }

    public function create(): Response
    {
        $tenants = Tenant::all()->map(fn ($t) => [
            'id' => $t->id,
            'name' => $t->getAttribute('data')['name'] ?? $t->getTenantKey(),
        ]);

        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get()->map(fn (Plan $p) => [
            'id' => $p->id,
            'name' => $p->name,
            'monthly_price' => $p->monthly_price,
            'yearly_price' => $p->yearly_price,
            'features_json' => $p->features_json,
        ]);

        return Inertia::render('Subscriptions/Create', [
            'tenants' => $tenants,
            'plans' => $plans,
        ]);
    }

    public function store(StoreSubscriptionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // If plan_id is provided, pull pricing and features from the plan
        if (! empty($data['plan_id'])) {
            $plan = Plan::find($data['plan_id']);
            if ($plan) {
                $data['plan_name'] = $data['plan_name'] ?? $plan->name;
                $data['price'] = $data['price'] ?? ($plan->monthly_price ?? $plan->yearly_price);
                $data['billing_cycle'] = $data['billing_cycle'] ?? ($plan->monthly_price ? 'monthly' : 'yearly');
                $data['features_json'] = $plan->features_json;
            }
        }

        Subscription::create($data);

        return redirect()->route('subscriptions.index')
            ->with('success', 'Subscription created successfully.');
    }

    public function show(Subscription $subscription): Response
    {
        return Inertia::render('Subscriptions/Show', [
            'subscription' => [
                'id' => $subscription->id,
                'plan_name' => $subscription->plan_name,
                'tenant_id' => $subscription->tenant_id,
                'tenant_name' => $subscription->tenant?->getAttribute('data')['name'] ?? $subscription->tenant_id,
                'billing_cycle' => $subscription->billing_cycle,
                'price' => (float) $subscription->price,
                'start_date' => $subscription->start_date?->toISOString(),
                'end_date' => $subscription->end_date?->toISOString(),
                'status' => $subscription->status->value,
                'auto_renew' => $subscription->auto_renew,
                'features_json' => $subscription->features_json ?? [],
                'created_at' => $subscription->created_at?->toISOString(),
                'updated_at' => $subscription->updated_at?->toISOString(),
            ],
        ]);
    }

    public function edit(Subscription $subscription): Response
    {
        $tenants = Tenant::all()->map(fn ($t) => [
            'id' => $t->id,
            'name' => $t->getAttribute('data')['name'] ?? $t->getTenantKey(),
        ]);

        return Inertia::render('Subscriptions/Edit', [
            'subscription' => [
                'id' => $subscription->id,
                'tenant_id' => $subscription->tenant_id,
                'plan_name' => $subscription->plan_name,
                'billing_cycle' => $subscription->billing_cycle,
                'price' => (float) $subscription->price,
                'start_date' => $subscription->start_date?->format('Y-m-d'),
                'end_date' => $subscription->end_date?->format('Y-m-d'),
                'status' => $subscription->status->value,
                'features_json' => $subscription->features_json ?? [],
            ],
            'tenants' => $tenants,
        ]);
    }

    public function update(UpdateSubscriptionRequest $request, Subscription $subscription): RedirectResponse
    {
        $subscription->update($request->validated());

        return redirect()->route('subscriptions.index')
            ->with('success', 'Subscription updated successfully.');
    }

    public function destroy(Subscription $subscription): RedirectResponse
    {
        $subscription->delete();

        return redirect()->route('subscriptions.index')
            ->with('success', 'Subscription deleted successfully.');
    }
}
