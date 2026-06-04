<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class PlanController extends Controller
{
    public function index(): Response
    {
        $plans = Plan::withCount('subscriptions')->orderBy('sort_order')->get()->map(fn (Plan $plan) => [
            'id' => $plan->id,
            'name' => $plan->name,
            'slug' => $plan->slug,
            'description' => $plan->description,
            'monthly_price' => $plan->monthly_price,
            'yearly_price' => $plan->yearly_price,
            'is_active' => $plan->is_active,
            'sort_order' => $plan->sort_order,
            'features_json' => $plan->features_json ?? [],
            'subscriptions_count' => $plan->subscriptions_count,
        ]);

        return Inertia::render('Plans/Index', ['plans' => $plans]);
    }

    public function create(): Response
    {
        return Inertia::render('Plans/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:plans,slug'],
            'description' => ['nullable', 'string'],
            'monthly_price' => ['nullable', 'numeric', 'min:0'],
            'yearly_price' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'max_users_per_tenant' => ['nullable', 'integer', 'min:1'],
            'storage_gb' => ['nullable', 'integer', 'min:0'],
            'features_json' => ['nullable', 'array'],
        ]);

        Plan::create($validated);

        return Redirect::route('plans.index')->with('flash', 'Plan created successfully.');
    }

    public function edit(Plan $plan): Response
    {
        return Inertia::render('Plans/Edit', [
            'plan' => [
                'id' => $plan->id,
                'name' => $plan->name,
                'slug' => $plan->slug,
                'description' => $plan->description,
                'monthly_price' => $plan->monthly_price,
                'yearly_price' => $plan->yearly_price,
                'is_active' => $plan->is_active,
                'sort_order' => $plan->sort_order,
                'max_users_per_tenant' => $plan->max_users_per_tenant,
                'storage_gb' => $plan->storage_gb,
                'features_json' => $plan->features_json ?? [],
            ],
        ]);
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:plans,slug,'.$plan->id],
            'description' => ['nullable', 'string'],
            'monthly_price' => ['nullable', 'numeric', 'min:0'],
            'yearly_price' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'max_users_per_tenant' => ['nullable', 'integer', 'min:1'],
            'storage_gb' => ['nullable', 'integer', 'min:0'],
            'features_json' => ['nullable', 'array'],
        ]);

        $plan->update($validated);

        return Redirect::route('plans.index')->with('flash', 'Plan updated successfully.');
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        $plan->delete();

        return Redirect::route('plans.index')->with('flash', 'Plan deleted successfully.');
    }
}
