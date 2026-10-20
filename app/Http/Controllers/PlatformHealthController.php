<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Services\Platform\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Platform health: tenant database reachability, queue/broadcast state and
 * subscriptions that need attention.
 */
class PlatformHealthController extends Controller
{
    public function __construct(private readonly TenantService $tenants) {}

    public function index(Request $request): Response
    {
        $tenants = Tenant::query()->with(['domains', 'subscription.plan'])->orderBy('id')->get();

        $rows = $tenants->map(function (Tenant $tenant) {
            $health = $this->tenants->health($tenant);
            $usage = $this->tenants->usage($tenant);
            $subscription = $tenant->subscription;

            return [
                'id' => $tenant->getTenantKey(),
                'name' => $tenant->displayName(),
                'domain' => $tenant->primaryDomain(),
                'is_active' => $tenant->isActive(),
                'reachable' => $health['reachable'],
                'migrations_ok' => $health['migrations_ok'],
                'users' => $health['users'],
                'patients' => $usage['patients'],
                'plan_name' => $subscription?->plan_name,
                'subscription_status' => $subscription?->effectiveStatus()->label(),
                'subscription_tone' => $subscription?->effectiveStatus()->tone(),
                'end_date' => $subscription?->end_date?->toDateString(),
                'days_remaining' => $subscription?->daysRemaining(),
                'last_seen_at' => $tenant->last_seen_at?->toDateTimeString(),
            ];
        });

        return Inertia::render('Platform/Health', [
            'tenants' => $rows,
            'system' => [
                'database' => $this->databaseStatus(),
                'queue' => $this->queueStatus(),
                'broadcast_driver' => (string) config('broadcasting.default'),
                'mail_driver' => (string) config('mail.default'),
                'pending_jobs' => $this->pendingJobs(),
            ],
            'attention' => [
                'expiring' => $this->tenants->expiringSoon((int) config('platform.health.expiring_soon_days', 14))
                    ->map(fn (Subscription $subscription) => [
                        'tenant_id' => $subscription->tenant_id,
                        'tenant_name' => $subscription->tenant?->displayName() ?? $subscription->tenant_id,
                        'plan_name' => $subscription->plan_name,
                        'end_date' => $subscription->end_date?->toDateString(),
                        'days_remaining' => $subscription->daysRemaining(),
                    ])->values(),
                'past_due' => Subscription::query()
                    ->where('status', SubscriptionStatus::PAST_DUE->value)
                    ->pluck('tenant_id'),
            ],
        ]);
    }

    /**
     * @return array{ok: bool, message: string}
     */
    private function databaseStatus(): array
    {
        try {
            DB::connection('central')->getPdo();

            return ['ok' => true, 'message' => 'Central database reachable'];
        } catch (Throwable $exception) {
            return ['ok' => false, 'message' => $exception->getMessage()];
        }
    }

    /**
     * @return array{driver: string, ok: bool}
     */
    private function queueStatus(): array
    {
        return [
            'driver' => (string) config('queue.default'),
            'ok' => config('queue.default') !== null,
        ];
    }

    private function pendingJobs(): ?int
    {
        try {
            if (! DB::connection('central')->getSchemaBuilder()->hasTable('jobs')) {
                return null;
            }

            return (int) DB::connection('central')->table('jobs')->count();
        } catch (Throwable) {
            return null;
        }
    }
}
