<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\PlanFeature;
use App\Models\Announcement;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Read model for the public marketing site.
 *
 * The sales pages are not a parallel universe of hard-coded copy: every figure
 * they show is either a platform setting an operator edited in the console or
 * a live count from the landlord database. That means renaming the platform,
 * editing support contacts, publishing a plan or taking a banner live updates
 * the public site without a deploy.
 *
 * Each section degrades to an empty value when its table is missing, so a
 * tenant-only install or a fresh database still renders the marketing pages
 * instead of throwing.
 */
final class MarketingContent
{
    /**
     * Everything the public pages need, in one prop.
     *
     * @return array<string, mixed>
     */
    public static function toArray(): array
    {
        $settings = PlatformContext::settings();
        $plans = self::plans();

        return [
            'platform' => [
                'name' => (string) ($settings['platform_name'] ?? 'SanaGo'),
                'tagline' => (string) ($settings['platform_tagline'] ?? ''),
                'support_email' => (string) ($settings['support_email'] ?? ''),
                'support_phone' => $settings['support_phone'] !== null
                    ? (string) $settings['support_phone']
                    : null,
                'currency' => (string) ($settings['currency'] ?? 'XAF'),
                'currency_symbol' => (string) ($settings['currency_symbol'] ?? 'FCFA'),
                'default_locale' => (string) ($settings['default_locale'] ?? 'en'),
                'supported_locales' => array_values((array) ($settings['supported_locales'] ?? ['en'])),
                'trial_days' => (int) ($settings['trial_days'] ?? 14),
                'grace_days' => (int) ($settings['grace_days'] ?? 3),
            ],
            'plans' => $plans,
            'capabilities' => self::capabilities(),
            'stats' => self::stats($plans),
            'announcements' => self::announcements(),
            'billing_cycles' => ['monthly', 'yearly'],
        ];
    }

    /**
     * Active plans in operator-defined order, with the shape the pricing grid
     * and the capability matrix both consume.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function plans(): array
    {
        try {
            if (! Schema::connection('central')->hasTable('plans')) {
                return [];
            }

            return Plan::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('monthly_price')
                ->get()
                ->map(fn (Plan $plan): array => self::planPayload($plan))
                ->all();
        } catch (Throwable) {
            return [];
        }
    }

    /**
     * @return array<string, mixed>
     */
    private static function planPayload(Plan $plan): array
    {
        $features = [];

        foreach ((array) ($plan->features_json ?? []) as $feature) {
            $label = is_array($feature)
                ? ($feature['feature_label'] ?? $feature['label'] ?? null)
                : (is_string($feature) ? $feature : null);

            if (! is_string($label) || trim($label) === '') {
                continue;
            }

            $features[] = [
                'label' => $label,
                'highlighted' => (bool) (is_array($feature) ? ($feature['is_highlighted'] ?? false) : false),
            ];
        }

        return [
            'id' => $plan->getKey(),
            'name' => $plan->name,
            'slug' => $plan->slug,
            'description' => $plan->description,
            'monthly_price' => (float) $plan->monthly_price,
            'yearly_price' => (float) ($plan->yearly_price ?? ((float) $plan->monthly_price * 12)),
            'currency' => $plan->currency ?: PlatformContext::currency(),
            'is_featured' => (bool) $plan->is_featured,
            'trial_days' => $plan->trialDays(),
            'grace_days' => $plan->graceDays(),
            'limits' => $plan->limits(),
            'api_rate_limit' => $plan->api_rate_limit,
            'features' => $features,
            'capabilities' => $plan->featureFlags(),
        ];
    }

    /**
     * The module catalogue the pricing matrix and the feature pages describe.
     * Labels and one-line descriptions come from the PlanFeature enum, so the
     * public site can never drift from what the product actually gates.
     *
     * @return array<int, array{key: string, label: string, description: string}>
     */
    public static function capabilities(): array
    {
        return array_map(
            fn (PlanFeature $feature): array => [
                'key' => $feature->value,
                'label' => $feature->label(),
                'description' => $feature->description(),
            ],
            PlanFeature::cases(),
        );
    }

    /**
     * Live figures for the proof band. Only what is genuinely countable is
     * published — no invented percentages.
     *
     * @param  array<int, array<string, mixed>>  $plans
     * @return array<string, int|string|null>
     */
    private static function stats(array $plans): array
    {
        return [
            'hospitals' => self::liveHospitalCount(),
            'capabilities' => count(PlanFeature::cases()),
            'plans' => count($plans),
            'trial_days' => (int) PlatformContext::setting('trial_days', 14),
            'locales' => count((array) PlatformContext::setting('supported_locales', ['en'])),
        ];
    }

    private static function liveHospitalCount(): int
    {
        try {
            if (! Schema::connection('central')->hasTable('tenants')) {
                return 0;
            }

            return Tenant::query()
                ->where('is_active', true)
                ->whereNull('archived_at')
                ->count();
        } catch (Throwable) {
            return 0;
        }
    }

    /**
     * Published, unexpired announcements — the operator's megaphone for the
     * public strip above the header.
     *
     * @return array<int, array<string, mixed>>
     */
    private static function announcements(): array
    {
        try {
            if (! Schema::connection('central')->hasTable('announcements')) {
                return [];
            }

            return Announcement::query()
                ->where('is_published', true)
                ->where(function ($query): void {
                    $query->whereNull('expires_at')->orWhere('expires_at', '>=', now());
                })
                ->orderByDesc('published_at')
                ->limit(2)
                ->get()
                ->map(fn (Announcement $announcement): array => [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'body' => $announcement->body,
                    'type' => $announcement->type,
                    'published_at' => $announcement->published_at?->toISOString(),
                ])
                ->all();
        } catch (Throwable) {
            return [];
        }
    }
}
