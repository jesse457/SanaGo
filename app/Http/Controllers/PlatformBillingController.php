<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\SubscriptionInvoiceStatus;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\Tenant;
use App\Services\Platform\SubscriptionService;
use App\Services\Platform\TenantService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Billing & usage across all tenants: revenue, outstanding invoices and
 * per-tenant consumption of the plan quotas.
 */
class PlatformBillingController extends Controller
{
    public function __construct(
        private readonly SubscriptionService $subscriptions,
        private readonly TenantService $tenants,
    ) {}

    public function index(Request $request): Response
    {
        $status = (string) $request->query('status', 'all');

        $rows = Subscription::query()
            ->with(['tenant.domains', 'plan'])
            ->orderBy('tenant_id')
            ->get()
            ->map(function (Subscription $subscription) {
                $tenant = $subscription->tenant;
                $usage = $tenant !== null ? $this->tenants->usage($tenant) : ['users' => 0, 'patients' => 0, 'appointments' => 0, 'invoices' => 0];
                $limits = $subscription->limits();

                return [
                    'tenant_id' => $subscription->tenant_id,
                    'tenant_name' => $tenant?->displayName() ?? $subscription->tenant_id,
                    'plan_name' => $subscription->plan_name,
                    'billing_cycle' => $subscription->billing_cycle,
                    'price' => (float) $subscription->price,
                    'currency' => $subscription->currency,
                    'status' => $subscription->effectiveStatus()->value,
                    'status_label' => $subscription->effectiveStatus()->label(),
                    'status_tone' => $subscription->effectiveStatus()->tone(),
                    'end_date' => $subscription->end_date?->toDateString(),
                    'users' => ['used' => $usage['users'], 'limit' => $limits['users'] ?? null],
                    'patients' => ['used' => $usage['patients'], 'limit' => $limits['patients'] ?? null],
                    'storage_gb' => ['used' => 0, 'limit' => $limits['storage_gb'] ?? null],
                    'outstanding' => (float) SubscriptionInvoice::query()
                        ->where('tenant_id', $subscription->tenant_id)
                        ->outstanding()
                        ->sum('total'),
                ];
            })
            ->filter(fn (array $row) => $status === 'all' || $row['status'] === $status)
            ->values();

        $invoiceStatus = (string) $request->query('invoice_status', 'all');

        $invoices = SubscriptionInvoice::query()
            ->with('tenant.domains')
            ->when(
                $invoiceStatus !== 'all',
                fn ($query) => $query->where('status', $invoiceStatus),
            )
            ->latest()
            ->paginate(25)
            ->withQueryString()
            ->through(fn (SubscriptionInvoice $invoice) => [
                'id' => $invoice->id,
                'number' => $invoice->number,
                'tenant_id' => $invoice->tenant_id,
                'tenant_name' => $invoice->tenant?->displayName() ?? $invoice->tenant_id,
                'total' => (float) $invoice->total,
                'currency' => $invoice->currency,
                'status' => $invoice->status->value,
                'period_start' => $invoice->period_start?->toDateString(),
                'period_end' => $invoice->period_end?->toDateString(),
                'due_at' => $invoice->due_at?->toDateString(),
                'paid_at' => $invoice->paid_at?->toDateTimeString(),
            ]);

        return Inertia::render('Platform/Billing', [
            'rows' => $rows,
            'invoices' => $invoices,
            'revenue' => $this->subscriptions->revenueSummary(),
            'totals' => [
                'outstanding' => (float) SubscriptionInvoice::query()->outstanding()->sum('total'),
                'collected' => (float) SubscriptionInvoice::query()
                    ->where('status', SubscriptionInvoiceStatus::PAID->value)
                    ->sum('total'),
                'tenants' => Tenant::query()->where('is_active', true)->whereNull('archived_at')->count(),
            ],
            'filters' => [
                'status' => $status,
                'invoice_status' => $invoiceStatus,
            ],
            'invoiceStatuses' => SubscriptionInvoiceStatus::values(),
        ]);
    }
}
