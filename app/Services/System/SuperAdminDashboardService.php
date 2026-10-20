<?php

declare(strict_types=1);

namespace App\Services\System;

use App\Models\DemoRequest;
use App\Models\PlatformAuditLog;
use App\Models\Subscription;
use App\Models\SupportTicket;
use App\Models\Tenant;
use App\Services\Platform\SubscriptionService;

/**
 * Metrics for the landlord dashboard.
 *
 * Revenue is real MRR (annual plans divided by 12) instead of integer-cast
 * totals, and the activity feed reads the platform audit log rather than
 * fabricating "tenant created" rows from the tenant table.
 */
class SuperAdminDashboardService
{
    public function __construct(private readonly SubscriptionService $subscriptions) {}

    /**
     * @return array<string, mixed>
     */
    public function getMetrics(): array
    {
        $revenue = $this->subscriptions->revenueSummary();

        return [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('is_active', true)->whereNull('archived_at')->count(),
            'suspended_tenants' => Tenant::whereNotNull('suspended_at')->count(),
            'archived_tenants' => Tenant::whereNotNull('archived_at')->count(),
            'active_subscriptions' => $revenue['active'],
            'trialing_subscriptions' => $revenue['trialing'],
            'past_due_subscriptions' => $revenue['past_due'],
            'mrr' => $revenue['mrr'],
            'arr' => $revenue['arr'],
            'currency' => $revenue['currency'],
            'pending_demo_requests' => DemoRequest::where('pipeline_status', 'new')->count(),
            'open_tickets' => SupportTicket::whereIn('status', ['open', 'in_progress'])->count(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getRecentTenants(int $limit = 5): array
    {
        return Tenant::with(['subscription', 'domains'])
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn (Tenant $tenant) => [
                'id' => $tenant->getTenantKey(),
                'name' => $tenant->displayName(),
                'domain' => $tenant->domains->first()?->domain,
                'is_active' => $tenant->isActive(),
                'plan_name' => $tenant->subscription?->plan_name,
                'status' => $tenant->subscription?->effectiveStatus()->label(),
                'created_at' => $tenant->created_at?->toDateTimeString(),
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getRecentActivity(int $limit = 10): array
    {
        return PlatformAuditLog::query()
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn (PlatformAuditLog $log) => [
                'type' => $log->action,
                'label' => $log->description ?? $log->action,
                'detail' => $log->actor_name,
                'tenant_id' => $log->tenant_id,
                'time' => $log->created_at?->diffForHumans(),
                'created_at' => $log->created_at?->toDateTimeString(),
            ])
            ->all();
    }

    /**
     * Plan distribution across tenants (usage overview).
     *
     * @return array<int, array{plan: string, count: int}>
     */
    public function planDistribution(): array
    {
        return Subscription::query()
            ->selectRaw('plan_name as plan, count(*) as count')
            ->groupBy('plan_name')
            ->orderByDesc('count')
            ->get()
            ->map(fn ($row) => ['plan' => (string) $row->plan, 'count' => (int) $row->count])
            ->all();
    }
}
