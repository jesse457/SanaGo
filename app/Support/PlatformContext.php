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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Read model for platform + tenant plan context.
 *
 * Every result is backed by the application cache (see ::flush() for
 * invalidation). All lookups degrade gracefully: a missing central table,
 * an unreachable tenant DB or an unconfigured plan only removes a
 * capability, it never throws.
 */
final class PlatformContext
{
    /** @var array<string, mixed> */
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

    private const SETTINGS_CACHE_KEY = 'platform_context:settings';

    private const VERSION_CACHE_KEY = 'platform_context:version';

    // ---------------------------------------------------------------------
    // Settings
    // ---------------------------------------------------------------------

    /** @return array<string, mixed> */
    public static function settings(): array
    {
        return Cache::remember(
            self::SETTINGS_CACHE_KEY,
            now()->addHour(),
            static function (): array {
                $stored = [];

                try {
                    if (Schema::connection('central')->hasTable('platform_settings')) {
                        // The model already casts `value` to JSON — do not
                        // decode a second time.
                        $stored = PlatformSetting::query()
                            ->get()
                            ->mapWithKeys(static fn (PlatformSetting $s) => [$s->key => $s->value])
                            ->all();
                    }
                } catch (Throwable) {
                    // No landlord DB — use defaults.
                }

                return array_merge(self::DEFAULTS, $stored);
            },
        );
    }

    public static function setting(string $key, mixed $default = null): mixed
    {
        return self::settings()[$key] ?? $default ?? self::DEFAULTS[$key] ?? null;
    }

    public static function currency(): string
    {
        return (string) self::setting('currency', 'XAF');
    }

    /** @return array<string, mixed> */
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

    // ---------------------------------------------------------------------
    // Tenant / plan context
    // ---------------------------------------------------------------------

    public static function subscription(?Tenant $tenant): ?Subscription
    {
        return $tenant === null ? null : self::forTenant($tenant)['subscription_model'];
    }

    /**
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
        $tenantKey = (string) $tenant->getTenantKey();

        /** @var array{
         *     subscription: array<string, mixed>|null,
         *     subscription_model: Subscription|null,
         *     limits: array<string, int|null>,
         *     features: array<string, bool>,
         *     plan: Plan|null
         * } $context
         */
        $context = Cache::remember(
            self::tenantCacheKey($tenantKey),
            now()->addMinutes(10),
            static function () use ($tenant): array {
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

                return [
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
                    'plan' => $plan,
                ];
            },
        );

        // Usage depends on the *current* request's tenancy — compute (or
        // fetch a short-lived cache entry) outside the long-lived context.
        $context['usage'] = self::usage($tenant);

        return $context;
    }

    /** @return array<string, bool> */
    public static function features(?Tenant $tenant): array
    {
        return $tenant === null
            ? array_fill_keys(PlanFeature::core(), true)
            : self::forTenant($tenant)['features'];
    }

    public static function featureEnabled(?Tenant $tenant, PlanFeature|string $feature): bool
    {
        $key = $feature instanceof PlanFeature ? $feature->value : $feature;

        return (bool) (self::features($tenant)[$key] ?? false);
    }

    public static function limit(?Tenant $tenant, string $key): ?int
    {
        return $tenant === null ? null : (self::forTenant($tenant)['limits'][$key] ?? null);
    }

    /**
     * Cheap usage counters used for quota enforcement and usage meters.
     *
     * Only computed when the given tenant is the *current* tenancy — a
     * non-tenant request always reports zeros (and never caches them).
     *
     * @return array<string, int>
     */
    public static function usage(Tenant $tenant): array
    {
        if (tenancy()->tenant === null
            || tenancy()->tenant->getTenantKey() !== $tenant->getTenantKey()) {
            return ['users' => 0, 'patients' => 0, 'storage_gb' => 0];
        }

        return Cache::remember(
            self::tenantCacheKey((string) $tenant->getTenantKey(), 'usage'),
            now()->addMinutes(2),
            static function (): array {
                $usage = ['users' => 0, 'patients' => 0, 'storage_gb' => 0];

                try {
                    if (Schema::hasTable('users')) {
                        $usage['users'] = (int) User::query()->count();
                    }
                    if (Schema::hasTable('patients')) {
                        $usage['patients'] = (int) Patient::query()->count();
                    }
                } catch (Throwable) {
                    // Tenant DB not ready — report zeros.
                }

                return $usage;
            },
        );
    }

    // ---------------------------------------------------------------------
    // Invalidation
    // ---------------------------------------------------------------------

    /**
     * Forget cached context.
     *
     * - Pass a tenant to forget just that tenant.
     * - Pass nothing to forget settings *and* every tenant (via a version bump
     *   so stale entries become unreachable — works on any cache driver,
     *   no cache tags required).
     */
    public static function flush(?Tenant $tenant = null): void
    {
        Cache::forget(self::SETTINGS_CACHE_KEY);

        if ($tenant !== null) {
            $key = (string) $tenant->getTenantKey();
            Cache::forget(self::tenantCacheKey($key));
            Cache::forget(self::tenantCacheKey($key, 'usage'));

            return;
        }

        Cache::forever(self::VERSION_CACHE_KEY, self::version() + 1);
    }

    // ---------------------------------------------------------------------
    // Internals
    // ---------------------------------------------------------------------

    private static function version(): int
    {
        return (int) Cache::get(self::VERSION_CACHE_KEY, 0);
    }

    private static function tenantCacheKey(string $tenantKey, string $suffix = ''): string
    {
        $suffix = $suffix === '' ? '' : ':'.$suffix;

        return sprintf(
            'platform_context:tenant:%d:%s%s',
            self::version(),
            $tenantKey,
            $suffix,
        );
    }
}
