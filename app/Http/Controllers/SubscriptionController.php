<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\SubscriptionStatus;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\Tenant;
use App\Services\Platform\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Platform-wide subscription overview.
 *
 * Creation, plan changes and renewal are performed on the tenant page
 * (TenantSubscriptionController) because a subscription always belongs to
 * exactly one tenant. This controller lists and inspects them.
 */
class SubscriptionController extends Controller
{
    public function __construct(private readonly SubscriptionService $subscriptions) {}

    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', 'all');

        $subscriptions = Subscription::query()
            ->with('tenant')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('plan_name', 'like', "%{$search}%")
                        ->orWhere('tenant_id', 'like', "%{$search}%");
                });
            })
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString()
            ->through(fn (Subscription $subscription) => [
                'id' => $subscription->id,
                'plan_name' => $subscription->plan_name,
                'tenant_id' => $subscription->tenant_id,
                'tenant_name' => $subscription->tenant?->displayName() ?? $subscription->tenant_id,
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
                'created_at' => $subscription->created_at?->toISOString(),
            ]);

        return Inertia::render('Subscriptions/Index', [
            'subscriptions' => $subscriptions,
            'filters' => ['search' => $search, 'status' => $status],
            'summary' => $this->subscriptions->revenueSummary(),
            'plans' => Plan::query()->orderBy('sort_order')->get(['id', 'name']),
            'statuses' => SubscriptionStatus::values(),
        ]);
    }

    public function show(Subscription $subscription): Response
    {
        $subscription->load(['tenant.domains', 'plan']);

        return Inertia::render('Subscriptions/Show', [
            'subscription' => [
                'id' => $subscription->id,
                'plan_name' => $subscription->plan_name,
                'tenant_id' => $subscription->tenant_id,
                'tenant_name' => $subscription->tenant?->displayName() ?? $subscription->tenant_id,
                'tenant_domain' => $subscription->tenant?->primaryDomain(),
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
                'features_json' => $subscription->features_json ?? [],
                'features' => $subscription->featureFlags(),
                'limits' => $subscription->limits(),
                'notes' => $subscription->notes,
                'created_at' => $subscription->created_at?->toISOString(),
                'updated_at' => $subscription->updated_at?->toISOString(),
            ],
            'invoices' => $subscription->invoices()
                ->latest()
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

    /**
     * Update the internal note on a subscription (terms are changed through
     * the tenant panel so proration and notifications always run).
     */
    public function update(Request $request, Subscription $subscription): RedirectResponse
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $subscription->update(['notes' => $validated['notes'] ?? null]);

        return redirect()->back()->with('success', 'Subscription note saved.');
    }

    /**
     * Deleting a subscription would orphan the tenant: route it through a
     * cancellation instead.
     */
    public function destroy(Subscription $subscription, SubscriptionService $service): RedirectResponse
    {
        $tenant = Tenant::query()->find($subscription->tenant_id);

        if ($tenant === null) {
            $subscription->delete();

            return redirect()->route('subscriptions.index')->with('success', 'Subscription deleted.');
        }

        $service->cancel($tenant, immediately: true, reason: 'Deleted from the platform panel');

        return redirect()->route('subscriptions.index')
            ->with('success', 'Subscription cancelled (history kept).');
    }
}
