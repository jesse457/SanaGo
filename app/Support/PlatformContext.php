<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\PlanFeature;
use App\Models\Patient;
use App\Models\Plan;
use App\Models\PlatformSetting;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Request-scoped read model for platform + tenant plan context.
 *
 * Everything here degrades gracefully: a missing central table, an
 * unreachable tenant database or an unconfigured plan must never take the
 * application down, it only removes a capability.
 */
final class PlatformContext
{
    /**
     * Defaults for every platform setting. Stored values override these.
     *
     * @var array<string, mixed>
     */
    private const DEFAULTS = [
        'platform_name' => 'SanaGo',
        'platform_tagline' => 'Hospital management for Cameroon',
        'currency' => 'XAF',
        'currency_symbol' => 'FCFA',
        'default_locale' => 'en',
        'supported_locales' => ['en', 'fr'],
        'trial_days' => 14,
        'grace_days' => 3,
        'support_email' => 'support@sanago.cm',
        'support_phone' => '+237 6 00 00 00 00',
        'realtime_enabled' => true,
        'email_notifications_enabled' => true,
        'maintenance_mode' => false,
        'maintenance_message' => null,
    ];

    /** @var array<string, mixed>|null */
    private static ?array $settings = null;

    /** @var array<string, array<string, mixed>|null> */
    private static array $tenantCache = [];

    /**
     * @return array<string, mixed>
     */
    public static function settings(): array
    {
        if (self::$settings !== null) {
            return self::$settings;
        }

        $stored = [];

        try {
            if (Schema::connection('central')->hasTable('platform_settings')) {
                // The model casts `value` to JSON already — do not decode a
                // second time or plain strings become null.
                $stored = PlatformSetting::query()
                    ->get()
                    ->mapWithKeys(fn (PlatformSetting $setting) => [$setting->key => $setting->value])
                    ->all();
            }
        } catch (Throwable) {
            // No landlord database (tenant-only deployment) — use defaults.
        }

        return self::$settings = array_merge(self::DEFAULTS, $stored);
    }

    public static function setting(string $key, mixed $default = null): mixed
    {
        return self::settings()[$key] ?? $default ?? self::DEFAULTS[$key] ?? null;
    }

    public static function currency(): string
    {
        return (string) self::setting('currency', 'XAF');
    }

    /**
     * Payload shared with every Inertia page (central and tenant contexts).
     *
     * @return array<string, mixed>
     */
    public static function toArray(): array
    {
        return [
            'name' => self::setting('platform_name'),
            'tagline' => self::setting('platform_tagline'),
            'currency' => self::currency(),
            'currency_symbol' => self::setting('currency_symbol'),
            'default_locale' => self::setting('default_locale'),
            'supported_locales' => self::setting('supported_locales'),
            'trial_days' => (int) self::setting('trial_days'),
            'support_email' => self::setting('support_email'),
            'support_phone' => self::setting('support_phone'),
            'realtime_enabled' => (bool) self::setting('realtime_enabled'),
            'email_notifications_enabled' => (bool) self::setting('email_notifications_enabled'),
            'maintenance_mode' => (bool) self::setting('maintenance_mode'),
            'maintenance_message' => self::setting('maintenance_message'),
        ];
    }

    public static function subscription(?Tenant $tenant): ?Subscription
    {
        if ($tenant === null) {
            return null;
        }

        return self::forTenant($tenant)['subscription_model'];
    }

    /**
     * Plan context for a tenant: subscription summary, quotas, features and
     * current usage.
     *
     * @return array{
     *     subscription: array<string, mixed>|null,
     *     subscription_model: Subscription|null,
     *     limits: array<string, int|null>,
     *     features: array<string, bool>,
     *     usage: array<string, int>,
     *     plan: Plan|null
     * }
     */
    public static function forTenant(Tenant $tenant): array
    {
        $key = (string) $tenant->getTenantKey();

        if (isset(self::$tenantCache[$key])) {
            /** @var array{subscription: array<string, mixed>|null, subscription_model: Subscription|null, limits: array<string, int|null>, features: array<string, bool>, usage: array<string, int>, plan: Plan|null} */
            return self::$tenantCache[$key];
        }

        $subscription = null;

        try {
            $subscription = Subscription::query()
                ->with('plan')
                ->where('tenant_id', $tenant->getTenantKey())
                ->first();
        } catch (Throwable) {
            $subscription = null;
        }

        $plan = $subscription?->plan;

        $limits = $subscription?->limits()
            ?? $plan?->limits()
            ?? ['users' => null, 'patients' => null, 'storage_gb' => null];

        $features = $subscription?->featureFlags()
            ?? $plan?->featureFlags()
            ?? array_fill_keys(PlanFeature::core(), true);

        return self::$tenantCache[$key] = [
            'subscription' => $subscription === null ? null : [
                'id' => $subscription->id,
                'plan_id' => $subscription->plan_id,
                'plan_name' => $subscription->plan_name ?? $plan?->name,
                'status' => $subscription->effectiveStatus()->value,
                'status_label' => $subscription->effectiveStatus()->label(),
                'status_tone' => $subscription->effectiveStatus()->tone(),
                'billing_cycle' => $subscription->billing_cycle,
                'price' => (float) $subscription->price,
                'currency' => $subscription->currency ?? self::currency(),
                'start_date' => $subscription->start_date?->toDateString(),
                'end_date' => $subscription->end_date?->toDateString(),
                'trial_ends_at' => $subscription->trial_ends_at?->toDateString(),
                'days_remaining' => $subscription->daysRemaining(),
                'auto_renew' => $subscription->auto_renew,
                'cancel_at_period_end' => $subscription->cancel_at_period_end,
                'is_on_trial' => $subscription->isOnTrial(),
                'grants_access' => $subscription->grantsAccess(),
            ],
            'subscription_model' => $subscription,
            'limits' => $limits,
            'features' => $features,
            'usage' => self::usage($tenant, $limits),
            'plan' => $plan,
        ];
    }

    public static function features(?Tenant $tenant): array
    {
        if ($tenant === null) {
            return array_fill_keys(PlanFeature::core(), true);
        }

        return self::forTenant($tenant)['features'];
    }

    public static function featureEnabled(?Tenant $tenant, PlanFeature|string $feature): bool
    {
        $key = $feature instanceof PlanFeature ? $feature->value : $feature;

        return (bool) (self::features($tenant)[$key] ?? false);
    }

    public static function limit(?Tenant $tenant, string $key): ?int
    {
        if ($tenant === null) {
            return null;
        }

        return self::forTenant($tenant)['limits'][$key] ?? null;
    }

    /**
     * Cheap usage counters used for quota enforcement and the usage meters.
     * Counts run against the tenant database (tenant requests) and are
     * memoised per request.
     *
     * @param  array<string, int|null>  $limits
     * @return array<string, int>
     */
    public static function usage(Tenant $tenant, array $limits = []): array
    {
        $usage = ['users' => 0, 'patients' => 0, 'storage_gb' => 0];

        if (tenancy()->tenant === null || tenancy()->tenant->getTenantKey() !== $tenant->getTenantKey()) {
            return $usage;
        }

        try {
            if (Schema::hasTable('users')) {
                $usage['users'] = (int) User::query()->count();
            }

            if (Schema::hasTable('patients')) {
                $usage['patients'] = (int) Patient::query()->count();
            }
        } catch (Throwable) {
            // Tenant database not ready — report zero rather than crashing.
        }

        return $usage;
    }

    /**
     * Forget memoised context (tests, tenant switches, settings updates).
     */
    public static function flush(): void
    {
        self::$settings = null;
        self::$tenantCache = [];
    }
}
