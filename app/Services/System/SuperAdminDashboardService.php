<?php

declare(strict_types=1);

namespace App\Services\System;

use App\Models\DemoRequest;
use App\Models\Subscription;
use App\Models\SupportTicket;
use App\Models\Tenant;

class SuperAdminDashboardService
{
    public function getMetrics(): array
    {
        return [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('is_active', true)->count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'monthly_revenue' => (int) Subscription::where('status', 'active')
                ->where('billing_cycle', 'monthly')
                ->sum('price'),
            'yearly_revenue' => (int) Subscription::where('status', 'active')
                ->where('billing_cycle', 'yearly')
                ->sum('price'),
            'pending_demo_requests' => DemoRequest::where('pipeline_status', 'new')->count(),
            'open_tickets' => SupportTicket::whereIn('status', ['open', 'in_progress'])->count(),
        ];
    }

    public function getRecentTenants(int $limit = 5): array
    {
        return Tenant::with(['subscription', 'domains'])
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn (Tenant $tenant) => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'domain' => $tenant->domains->first()?->domain,
                'is_active' => $tenant->isActive(),
                'plan_name' => $tenant->subscription?->plan_name,
                'created_at' => $tenant->created_at?->toDateTimeString(),
            ])
            ->toArray();
    }

    public function getRecentActivity(int $limit = 10): array
    {
        $activities = [];

        $recentTenants = Tenant::latest()->take($limit)->get();
        foreach ($recentTenants as $tenant) {
            $activities[] = [
                'type' => 'tenant_created',
                'label' => 'New tenant registered',
                'detail' => $tenant->name,
                'time' => $tenant->created_at?->diffForHumans(),
            ];
        }

        usort($activities, fn ($a, $b) => $b['time'] <=> $a['time']);

        return array_slice($activities, 0, $limit);
    }
}
