<?php

declare(strict_types=1);

namespace App\Services\Platform;

use App\Enums\PlanFeature;
use App\Models\Patient;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\PlanLimitNotification;
use App\Services\System\NotificationService;
use App\Support\PlatformContext;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Enforces the quotas and capabilities of a tenant's plan.
 *
 * Quotas are read through {@see PlatformContext} so per-tenant overrides on
 * the subscription automatically apply. A `null` limit means unlimited.
 */
class PlanLimitService
{
    /** @var array<string, bool> */
    private array $warned = [];

    public function __construct(private readonly NotificationService $notifications) {}

    /**
     * Current usage for a quota key.
     */
    public function usage(string $key, ?Tenant $tenant = null): int
    {
        $tenant ??= tenant();

        if ($tenant === null) {
            return 0;
        }

        try {
            return match ($key) {
                'users' => Schema::hasTable('users') ? User::query()->count() : 0,
                'patients' => Schema::hasTable('patients')
                    ? Patient::query()->count()
                    : 0,
                default => 0,
            };
        } catch (Throwable) {
            return 0;
        }
    }

    public function limit(string $key, ?Tenant $tenant = null): ?int
    {
        return PlatformContext::limit($tenant ?? tenant(), $key);
    }

    /**
     * Remaining allowance, or null when unlimited.
     */
    public function remaining(string $key, ?Tenant $tenant = null): ?int
    {
        $limit = $this->limit($key, $tenant);

        if ($limit === null) {
            return null;
        }

        return max(0, $limit - $this->usage($key, $tenant));
    }

    public function exceeded(string $key, int $additional = 1, ?Tenant $tenant = null): bool
    {
        $limit = $this->limit($key, $tenant);

        if ($limit === null) {
            return false;
        }

        return $this->usage($key, $tenant) + $additional > $limit;
    }

    /**
     * Throw a validation error when the quota would be exceeded.
     *
     * @throws ValidationException
     */
    public function ensureCan(string $key, int $additional = 1, ?Tenant $tenant = null): void
    {
        if (! $this->exceeded($key, $additional, $tenant)) {
            $this->warnIfClose($key, $tenant);

            return;
        }

        $limit = $this->limit($key, $tenant);
        $label = $key === 'users' ? 'user seats' : $key;

        throw ValidationException::withMessages([
            $key => sprintf(
                'Your plan allows %d %s and you are already using %d. Upgrade your plan to add more.',
                (int) $limit,
                $label,
                $this->usage($key, $tenant),
            ),
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function ensureFeature(PlanFeature|string $feature, ?Tenant $tenant = null): void
    {
        $tenant ??= tenant();

        if ($tenant === null || PlatformContext::featureEnabled($tenant, $feature)) {
            return;
        }

        $key = $feature instanceof PlanFeature ? $feature->value : $feature;
        $label = $feature instanceof PlanFeature ? $feature->label() : ucfirst($key);

        throw ValidationException::withMessages([
            'plan' => sprintf('%s is not included in your current plan.', $label),
        ]);
    }

    public function featureEnabled(PlanFeature|string $feature, ?Tenant $tenant = null): bool
    {
        return PlatformContext::featureEnabled($tenant ?? tenant(), $feature);
    }

    /**
     * @return array<string, array{limit: int|null, used: int, remaining: int|null}>
     */
    public function summary(?Tenant $tenant = null): array
    {
        $summary = [];

        foreach (['users', 'patients', 'storage_gb'] as $key) {
            $summary[$key] = [
                'limit' => $this->limit($key, $tenant),
                'used' => $this->usage($key, $tenant),
                'remaining' => $this->remaining($key, $tenant),
            ];
        }

        return $summary;
    }

    /**
     * Nudge tenant admins once when they are close to a quota.
     */
    private function warnIfClose(string $key, ?Tenant $tenant): void
    {
        $tenant ??= tenant();

        if ($tenant === null) {
            return;
        }

        $limit = $this->limit($key, $tenant);

        if ($limit === null || $limit < 5) {
            return;
        }

        $used = $this->usage($key, $tenant);
        $threshold = (int) floor($limit * 0.9);

        if ($used < $threshold) {
            return;
        }

        $cacheKey = $key.'|'.$tenant->getTenantKey();

        if ($this->warned[$cacheKey] ?? false) {
            return;
        }

        $this->warned[$cacheKey] = true;

        $this->notifications->deliver(
            User::permission('manage-users')->get(),
            new PlanLimitNotification(
                limitKey: $key,
                message: sprintf('You have used %d of %d %s on your current plan.', $used, $limit, $key),
                link: '/billing',
                title: 'Approaching your plan limit',
            ),
        );
    }
}
