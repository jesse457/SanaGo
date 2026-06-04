<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreTenantRequest;
use App\Http\Requests\UpdateTenantRequest;
use App\Models\Tenant;
use App\Services\System\SuperAdminDashboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class TenantController extends Controller
{
    public function dashboard(SuperAdminDashboardService $dashboardService): Response
    {
        return Inertia::render('Dashboard/SuperAdmin', [
            'metrics' => $dashboardService->getMetrics(),
            'recentTenants' => $dashboardService->getRecentTenants(),
            'recentActivity' => $dashboardService->getRecentActivity(),
        ]);
    }

    public function index(): Response
    {
        $tenants = Tenant::with('domains', 'subscription')->latest()->get()->map(fn (Tenant $tenant) => [
            'id' => $tenant->id,
            'slug' => $tenant->getTenantKey(),
            'name' => $tenant->name,
            'domains' => $tenant->domains->pluck('domain'),
            'is_active' => $tenant->isActive(),
            'suspended_at' => $tenant->suspended_at?->toISOString(),
            'created_at' => $tenant->created_at?->toISOString(),
            'subscription' => $tenant->subscription ? [
                'plan_name' => $tenant->subscription->plan_name,
                'status' => $tenant->subscription->status->value,
            ] : null,
        ]);

        return Inertia::render('Tenants/Index', [
            'tenants' => $tenants,
        ]);
    }

    public function store(StoreTenantRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $tenant = Tenant::create([
            'id' => $validated['slug'],
            'data' => [
                'name' => $validated['name'],
            ],
        ]);

        $tenant->domains()->create([
            'domain' => $validated['domain'],
        ]);

        return redirect()->route('tenants.index')
            ->with('flash', 'Tenant created successfully.');
    }

    public function show(Tenant $tenant): Response
    {
        $tenant->load(['domains', 'subscription.plan', 'notes.user']);

        return Inertia::render('Tenants/Show', [
            'tenant' => [
                'id' => $tenant->id,
                'slug' => $tenant->getTenantKey(),
                'name' => $tenant->name,
                'domains' => $tenant->domains->pluck('domain'),
                'is_active' => $tenant->isActive(),
                'suspended_at' => $tenant->suspended_at?->toISOString(),
                'trial_ends_at' => $tenant->trial_ends_at?->toDateString(),
                'settings_json' => $tenant->settings_json,
                'created_at' => $tenant->created_at?->toISOString(),
                'updated_at' => $tenant->updated_at?->toISOString(),
                'subscription' => $tenant->subscription ? [
                    'id' => $tenant->subscription->id,
                    'plan_name' => $tenant->subscription->plan_name,
                    'plan' => $tenant->subscription->plan ? [
                        'id' => $tenant->subscription->plan->id,
                        'name' => $tenant->subscription->plan->name,
                    ] : null,
                    'billing_cycle' => $tenant->subscription->billing_cycle,
                    'price' => $tenant->subscription->price,
                    'start_date' => $tenant->subscription->start_date?->toDateString(),
                    'end_date' => $tenant->subscription->end_date?->toDateString(),
                    'status' => $tenant->subscription->status->value,
                    'auto_renew' => $tenant->subscription->auto_renew,
                ] : null,
                'notes' => $tenant->notes->map(fn ($note) => [
                    'id' => $note->id,
                    'note' => $note->note,
                    'user_name' => $note->user?->name,
                    'created_at' => $note->created_at?->toDateTimeString(),
                ]),
            ],
        ]);
    }

    public function edit(Tenant $tenant): Response
    {
        return Inertia::render('Tenants/Edit', [
            'tenant' => [
                'id' => $tenant->id,
                'slug' => $tenant->getTenantKey(),
                'name' => $tenant->name,
                'domains' => $tenant->domains->pluck('domain'),
            ],
        ]);
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validated();

        $data = $tenant->getAttribute('data') ?? [];
        $data['name'] = $validated['name'];
        $tenant->update(['data' => $data]);

        return redirect()->route('tenants.index')
            ->with('flash', 'Tenant updated successfully.');
    }

    public function toggleActive(Request $request, Tenant $tenant): RedirectResponse
    {
        if ($tenant->isActive()) {
            $tenant->update(['is_active' => false, 'suspended_at' => now()]);
        } else {
            $tenant->update(['is_active' => true, 'suspended_at' => null]);
        }

        return Redirect::back()->with('flash', 'Tenant status updated.');
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        $tenant->delete();

        return redirect()->route('tenants.index')
            ->with('flash', 'Tenant deleted successfully.');
    }

    public function billing(): Response
    {
        $tenant = tenant();

        if (! $tenant) {
            return Inertia::render('Billing/Index', ['subscription' => null, 'tenant_name' => '']);
        }

        $sub = $tenant->subscription;

        return Inertia::render('Billing/Index', [
            'subscription' => $sub ? [
                'plan_name' => $sub->plan_name,
                'billing_cycle' => $sub->billing_cycle,
                'price' => (string) $sub->price,
                'start_date' => $sub->start_date?->toDateString(),
                'end_date' => $sub->end_date?->toDateString(),
                'status' => $sub->status->value,
                'auto_renew' => $sub->auto_renew,
                'features_json' => $sub->features_json ?? [],
            ] : null,
            'tenant_name' => $tenant->name,
        ]);
    }
}
