<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreTenantRequest;
use App\Http\Requests\UpdateTenantRequest;
use App\Models\Note;
use App\Models\Plan;
use App\Models\SubscriptionInvoice;
use App\Models\Tenant;
use App\Services\Platform\PlatformAuditService;
use App\Services\Platform\SubscriptionService;
use App\Services\Platform\TenantService;
use App\Services\System\SuperAdminDashboardService;
use App\Support\PlatformContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Stancl\Tenancy\Database\Models\ImpersonationToken;

/**
 * Landlord tenant management: list, provision, inspect, suspend, archive and
 * (deliberately) purge a hospital.
 */
class TenantController extends Controller
{
    public function __construct(
        private readonly TenantService $tenants,
        private readonly SubscriptionService $subscriptions,
        private readonly PlatformAuditService $audit,
    ) {}

    public function dashboard(SuperAdminDashboardService $dashboardService): Response
    {
        return Inertia::render('Dashboard/SuperAdmin', [
            'metrics' => $dashboardService->getMetrics(),
            'recentTenants' => $dashboardService->getRecentTenants(),
            'recentActivity' => $dashboardService->getRecentActivity(),
            'revenue' => $this->subscriptions->revenueSummary(),
            'expiringSoon' => $this->tenants->expiringSoon(
                (int) config('platform.health.expiring_soon_days', 14)
            )->map(fn ($subscription) => [
                'tenant_id' => $subscription->tenant_id,
                'tenant_name' => $subscription->tenant?->displayName() ?? $subscription->tenant_id,
                'plan_name' => $subscription->plan_name,
                'end_date' => $subscription->end_date?->toDateString(),
                'days_remaining' => $subscription->daysRemaining(),
            ])->values(),
        ]);
    }

    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', 'all');

        $tenants = Tenant::query()
            ->with(['domains', 'subscription.plan'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('id', 'like', "%{$search}%")
                        ->orWhere('data', 'like', "%{$search}%")
                        ->orWhereHas('domains', fn ($domain) => $domain->where('domain', 'like', "%{$search}%"));
                });
            })
            ->when($status === 'active', fn ($query) => $query->where('is_active', true)->whereNull('archived_at'))
            ->when($status === 'suspended', fn ($query) => $query->whereNotNull('suspended_at'))
            ->when($status === 'archived', fn ($query) => $query->whereNotNull('archived_at'))
            ->latest()
            ->paginate(20)
            ->withQueryString()
            ->through(fn (Tenant $tenant) => [
                'id' => $tenant->getTenantKey(),
                'slug' => $tenant->getTenantKey(),
                'name' => $tenant->displayName(),
                'domains' => $tenant->domains->pluck('domain'),
                'is_active' => $tenant->isActive(),
                'suspended_at' => $tenant->suspended_at?->toISOString(),
                'archived_at' => $tenant->archived_at?->toISOString(),
                'created_at' => $tenant->created_at?->toISOString(),
                'subscription' => $tenant->subscription ? [
                    'plan_name' => $tenant->subscription->plan_name,
                    'status' => $tenant->subscription->effectiveStatus()->value,
                    'status_label' => $tenant->subscription->effectiveStatus()->label(),
                    'status_tone' => $tenant->subscription->effectiveStatus()->tone(),
                    'end_date' => $tenant->subscription->end_date?->toDateString(),
                    'days_remaining' => $tenant->subscription->daysRemaining(),
                ] : null,
            ]);

        return Inertia::render('Tenants/Index', [
            'tenants' => $tenants,
            'filters' => ['search' => $search, 'status' => $status],
            'counts' => [
                'all' => Tenant::count(),
                'active' => Tenant::where('is_active', true)->whereNull('archived_at')->count(),
                'suspended' => Tenant::whereNotNull('suspended_at')->count(),
                'archived' => Tenant::whereNotNull('archived_at')->count(),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Tenants/Create', [
            'plans' => Plan::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn (Plan $plan) => [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'monthly_price' => (float) $plan->monthly_price,
                    'yearly_price' => (float) $plan->yearly_price,
                    'trial_days' => $plan->trialDays(),
                    'currency' => $plan->currency,
                ]),
            'defaultTrialDays' => (int) PlatformContext::setting('trial_days', 14),
        ]);
    }

    public function store(StoreTenantRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $tenant = $this->tenants->provision([
            'slug' => $data['slug'],
            'name' => $data['name'],
            'domain' => $data['domain'],
            'admin_name' => $data['admin_name'] ?? null,
            'admin_email' => $data['admin_email'] ?? null,
            'admin_password' => $data['admin_password'] ?? null,
            'plan_id' => $data['plan_id'] ?? null,
            'billing_cycle' => $data['billing_cycle'] ?? 'monthly',
            'trial' => (bool) ($data['trial'] ?? true),
            'demo_data' => (bool) ($data['demo_data'] ?? false),
        ]);

        return redirect()
            ->route('tenants.show', $tenant->getTenantKey())
            ->with('success', 'Hospital provisioned. Share the admin credentials securely.');
    }

    public function show(Tenant $tenant): Response
    {
        $tenant->load(['domains', 'subscription.plan', 'notes']);

        $subscription = $tenant->subscription;
        $snapshot = $this->tenants->snapshot($tenant);

        return Inertia::render('Tenants/Show', [
            'tenant' => [
                'id' => $tenant->getTenantKey(),
                'slug' => $tenant->getTenantKey(),
                'name' => $tenant->displayName(),
                'domains' => $tenant->domains->pluck('domain'),
                'is_active' => $tenant->isActive(),
                'suspended_at' => $tenant->suspended_at?->toISOString(),
                'archived_at' => $tenant->archived_at?->toISOString(),
                'archived_reason' => $tenant->archived_reason,
                'trial_ends_at' => $tenant->trial_ends_at?->toDateString(),
                'settings_json' => $tenant->settings_json,
                'created_at' => $tenant->created_at?->toISOString(),
                'updated_at' => $tenant->updated_at?->toISOString(),
                'last_seen_at' => $tenant->last_seen_at?->toDateTimeString(),
                'subscription' => $subscription ? [
                    'id' => $subscription->id,
                    'plan_id' => $subscription->plan_id,
                    'plan_name' => $subscription->plan_name,
                    'plan' => $subscription->plan ? [
                        'id' => $subscription->plan->id,
                        'name' => $subscription->plan->name,
                        'features' => $subscription->plan->featureFlags(),
                    ] : null,
                    'billing_cycle' => $subscription->billing_cycle,
                    'price' => (float) $subscription->price,
                    'currency' => $subscription->currency,
                    'start_date' => $subscription->start_date?->toDateString(),
                    'end_date' => $subscription->end_date?->toDateString(),
                    'status' => $subscription->effectiveStatus()->value,
                    'status_label' => $subscription->effectiveStatus()->label(),
                    'status_tone' => $subscription->effectiveStatus()->tone(),
                    'days_remaining' => $subscription->daysRemaining(),
                    'auto_renew' => $subscription->auto_renew,
                    'cancel_at_period_end' => $subscription->cancel_at_period_end,
                    'limits' => $subscription->limits(),
                    'limit_overrides' => $subscription->limits_json ?? [],
                    'feature_overrides' => $subscription->feature_overrides_json ?? [],
                    'features' => $subscription->featureFlags(),
                    'notes' => $subscription->notes,
                ] : null,
                'usage' => $snapshot['usage'],
                'health' => $snapshot['health'],
                'users' => $snapshot['users'],
                'invoices' => $tenant->invoices()
                    ->latest()
                    ->limit(20)
                    ->get()
                    ->map(fn (SubscriptionInvoice $invoice) => [
                        'id' => $invoice->id,
                        'number' => $invoice->number,
                        'total' => (float) $invoice->total,
                        'currency' => $invoice->currency,
                        'status' => $invoice->status->value,
                        'period_start' => $invoice->period_start?->toDateString(),
                        'period_end' => $invoice->period_end?->toDateString(),
                        'paid_at' => $invoice->paid_at?->toDateTimeString(),
                    ]),
                'notes' => $tenant->notes->map(fn (Note $note) => [
                    'id' => $note->id,
                    'note' => $note->note,
                    'user_name' => $note->user?->name,
                    'created_at' => $note->created_at?->toDateTimeString(),
                ]),
            ],
            'plans' => Plan::query()->orderBy('sort_order')->get()->map(fn (Plan $plan) => [
                'id' => $plan->id,
                'name' => $plan->name,
                'slug' => $plan->slug,
                'monthly_price' => (float) $plan->monthly_price,
                'yearly_price' => (float) $plan->yearly_price,
                'currency' => $plan->currency,
                'sort_order' => $plan->sort_order,
                'limits' => $plan->limits(),
                // The change-plan dialog needs to show what buying this plan
                // actually adds, not just its list price.
                'features' => $plan->featureFlags(),
            ]),
        ]);
    }

    public function edit(Tenant $tenant): Response
    {
        return Inertia::render('Tenants/Edit', [
            'tenant' => [
                'id' => $tenant->getTenantKey(),
                'slug' => $tenant->getTenantKey(),
                'name' => $tenant->displayName(),
                'domains' => $tenant->domains->pluck('domain'),
            ],
        ]);
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validated();

        $tenant->name = $validated['name'];
        $tenant->save();

        // A wrong domain used to be unfixable from the panel.
        if (! empty($validated['domain'])) {
            $tenant->domains()->first()?->update(['domain' => $validated['domain']]);
        }

        $this->audit->log(
            action: 'tenant.updated',
            description: sprintf('%s updated', $validated['name']),
            subject: $tenant,
            tenantId: (string) $tenant->getTenantKey(),
            properties: $validated,
        );

        return redirect()->route('tenants.show', $tenant->getTenantKey())
            ->with('success', 'Hospital updated.');
    }

    public function suspend(Request $request, Tenant $tenant): RedirectResponse
    {
        $this->tenants->suspend($tenant, $request->string('reason')->trim()->value() ?: null);

        return redirect()->back()->with('success', 'Hospital suspended.');
    }

    public function reactivate(Tenant $tenant): RedirectResponse
    {
        $this->tenants->reactivate($tenant);

        return redirect()->back()->with('success', 'Hospital reactivated.');
    }

    /**
     * Retained for the toggle endpoint the existing UI uses.
     */
    public function toggleActive(Request $request, Tenant $tenant): RedirectResponse
    {
        if ($tenant->isActive()) {
            $this->tenants->suspend($tenant, $request->string('reason')->value() ?: null);
        } else {
            $this->tenants->reactivate($tenant);
        }

        return redirect()->back()->with('success', 'Hospital status updated.');
    }

    public function archive(Request $request, Tenant $tenant): RedirectResponse
    {
        $this->tenants->archive($tenant, $request->string('reason')->value() ?: null);

        return redirect()->route('tenants.index')->with('success', 'Hospital archived. Data is retained.');
    }

    public function restore(Tenant $tenant): RedirectResponse
    {
        $this->tenants->restore($tenant);

        return redirect()->route('tenants.show', $tenant->getTenantKey())
            ->with('success', 'Hospital restored from archive.');
    }

    public function destroy(Request $request, Tenant $tenant): RedirectResponse
    {
        // Deleting a tenant drops its database: require an explicit
        // confirmation string so a stray click can never erase a hospital.
        if ($request->string('confirm')->value() !== (string) $tenant->getTenantKey()) {
            return redirect()->back()->with(
                'error',
                'Type the hospital slug to confirm permanent deletion. Consider archiving instead.',
            );
        }

        $this->tenants->purge($tenant);

        return redirect()->route('tenants.index')->with('success', 'Hospital permanently deleted.');
    }

    public function addNote(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'note' => ['required', 'string', 'max:2000'],
        ]);

        $tenant->notes()->create([
            'user_id' => $request->user()->id,
            'note' => $validated['note'],
        ]);

        return redirect()->back()->with('success', 'Note added.');
    }

    /**
     * Create a one-time support-access token and send the operator to the
     * tenant's own domain to accept it.
     */
    public function impersonate(Request $request, Tenant $tenant): RedirectResponse
    {
        if (! config('platform.impersonation.enabled', true)) {
            return redirect()->back()->with('error', 'Support access is disabled on this platform.');
        }

        $validated = $request->validate([
            'user_id' => ['nullable', 'integer'],
        ]);

        $userId = $validated['user_id'] ?? null;

        if ($userId === null) {
            $admin = collect($this->tenants->users($tenant))
                ->first(fn (array $user) => in_array('admin', $user['roles'], true));

            if ($admin === null) {
                return redirect()->back()->with('error', 'This hospital has no administrator to impersonate.');
            }

            $userId = $admin['id'];
        }

        $domain = $tenant->primaryDomain();

        if ($domain === null) {
            return redirect()->back()->with('error', 'This hospital has no domain configured.');
        }

        // One-time token consumed by the tenant domain (see routes/tenant.php).
        $token = ImpersonationToken::create([
            'tenant_id' => $tenant->getTenantKey(),
            'user_id' => (string) $userId,
            'redirect_url' => url('/dashboard'),
            'auth_guard' => config('auth.defaults.guard'),
        ]);

        $this->audit->log(
            action: 'tenant.impersonation_started',
            description: sprintf('Support access to %s as user #%d', $tenant->displayName(), $userId),
            subject: $tenant,
            tenantId: (string) $tenant->getTenantKey(),
            properties: ['user_id' => $userId, 'token_id' => $token->id],
        );

        $request->session()->put('impersonated_by', $request->user()->id);

        $port = $request->getPort();
        $portSuffix = in_array($port, [80, 443], true) ? '' : ':'.$port;

        return redirect()->away(sprintf(
            '%s://%s%s/impersonate/%s',
            $request->getScheme(),
            $domain,
            $portSuffix,
            $token->token,
        ));
    }

    /**
     * Tenant-facing billing portal.
     */
    public function billing(): Response
    {
        $tenant = tenant();

        if ($tenant === null) {
            return Inertia::render('Billing/Index', [
                'subscription' => null,
                'tenant_name' => '',
                'invoices' => [],
                'features' => [],
                'limits' => [],
                'usage' => [],
            ]);
        }

        $context = PlatformContext::forTenant($tenant);

        return Inertia::render('Billing/Index', [
            'subscription' => $context['subscription'],
            'tenant_name' => $tenant->displayName(),
            'features' => $context['features'],
            'limits' => $context['limits'],
            'usage' => $context['usage'],
            'invoices' => $tenant->invoices()
                ->latest()
                ->limit(24)
                ->get()
                ->map(fn (SubscriptionInvoice $invoice) => [
                    'id' => $invoice->id,
                    'number' => $invoice->number,
                    'total' => (float) $invoice->total,
                    'currency' => $invoice->currency,
                    'status' => $invoice->status->value,
                    'period_start' => $invoice->period_start?->toDateString(),
                    'period_end' => $invoice->period_end?->toDateString(),
                    'paid_at' => $invoice->paid_at?->toDateTimeString(),
                ]),
        ]);
    }
}
