<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\Tenant;
use App\Services\Platform\PlanLimitService;
use App\Services\Platform\SubscriptionService;
use App\Services\Platform\TenantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Per-tenant plan and subscription control for the super admin.
 *
 * Every action is scoped by the `{tenant}` route parameter, so an operator
 * can only ever change the subscription of the hospital they opened.
 */
class TenantSubscriptionController extends Controller
{
    public function __construct(
        private readonly SubscriptionService $subscriptions,
        private readonly PlanLimitService $limits,
    ) {}

    /**
     * Assign a plan to a tenant without a subscription (or start a trial).
     */
    public function assign(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
            'billing_cycle' => ['required', 'in:monthly,yearly'],
            'trial' => ['boolean'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $plan = Plan::query()->findOrFail($validated['plan_id']);

        $this->subscriptions->assignPlan($tenant, $plan, [
            'billing_cycle' => $validated['billing_cycle'],
            'trial' => (bool) ($validated['trial'] ?? false),
            'price' => $validated['price'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', sprintf('%s assigned to %s.', $plan->name, $tenant->displayName()));
    }

    /**
     * Upgrade or downgrade the current plan.
     */
    public function changePlan(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
            'billing_cycle' => ['nullable', 'in:monthly,yearly'],
            'immediate' => ['boolean'],
            'price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $plan = Plan::query()->findOrFail($validated['plan_id']);

        $this->subscriptions->changePlan($tenant, $plan, [
            'billing_cycle' => $validated['billing_cycle'] ?? null,
            'immediate' => (bool) ($validated['immediate'] ?? false),
            'price' => $validated['price'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Plan updated.');
    }

    public function renew(Tenant $tenant): RedirectResponse
    {
        $this->subscriptions->renew($tenant);

        return redirect()->back()->with('success', 'Subscription renewed.');
    }

    public function extend(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'days' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        $this->subscriptions->extend($tenant, (int) $validated['days']);

        return redirect()->back()->with('success', sprintf('Subscription extended by %d days.', $validated['days']));
    }

    public function cancel(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'immediately' => ['boolean'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $this->subscriptions->cancel(
            $tenant,
            (bool) ($validated['immediately'] ?? false),
            $validated['reason'] ?? null,
        );

        return redirect()->back()->with('success', 'Subscription cancelled.');
    }

    public function resume(Tenant $tenant): RedirectResponse
    {
        $this->subscriptions->resume($tenant);

        return redirect()->back()->with('success', 'Subscription resumed.');
    }

    public function expire(Tenant $tenant): RedirectResponse
    {
        $this->subscriptions->expire($tenant);

        return redirect()->back()->with('success', 'Subscription expired.');
    }

    /**
     * Per-tenant quota and feature overrides.
     */
    public function overrides(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'limits' => ['array'],
            'limits.users' => ['nullable', 'integer', 'min:1'],
            'limits.patients' => ['nullable', 'integer', 'min:1'],
            'limits.storage_gb' => ['nullable', 'integer', 'min:1'],
            'features' => ['array'],
        ]);

        $this->subscriptions->updateOverrides(
            $tenant,
            $validated['limits'] ?? [],
            array_map(fn ($value) => filter_var($value, FILTER_VALIDATE_BOOLEAN), $validated['features'] ?? []),
        );

        return redirect()->back()->with('success', 'Plan overrides saved.');
    }

    /**
     * Mark a platform invoice as paid.
     */
    public function payInvoice(Request $request, Tenant $tenant, SubscriptionInvoice $invoice): RedirectResponse
    {
        abort_unless((string) $invoice->tenant_id === (string) $tenant->getTenantKey(), 404);

        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'max:30'],
            'reference' => ['nullable', 'string', 'max:100'],
        ]);

        $this->subscriptions->recordPayment($invoice, $validated);

        return redirect()->back()->with('success', 'Invoice marked paid.');
    }

    /**
     * Proration preview shown before a plan change (JSON, used by the panel).
     */
    public function preview(Request $request, Tenant $tenant): JsonResponse
    {
        $validated = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
            'billing_cycle' => ['nullable', 'in:monthly,yearly'],
        ]);

        $subscription = $this->subscriptions->forTenant($tenant);
        $plan = Plan::query()->findOrFail($validated['plan_id']);

        if ($subscription === null) {
            return response()->json([
                'amount_due' => $plan->priceFor($validated['billing_cycle'] ?? 'monthly'),
                'direction' => 'new',
                'days_remaining' => null,
            ]);
        }

        $proration = $this->subscriptions->proration($subscription, $plan, $validated['billing_cycle'] ?? null);

        return response()->json($proration);
    }

    /**
     * Usage vs limits for one tenant (used by the tenant page).
     */
    public function usage(Tenant $tenant): Response
    {
        return Inertia::render('Tenants/Show', [
            'usage' => app(TenantService::class)->usage($tenant),
        ]);
    }
}
