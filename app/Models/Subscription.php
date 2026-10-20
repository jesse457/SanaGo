<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use Carbon\CarbonImmutable;
use Database\Factories\SubscriptionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * The current plan agreement for exactly one tenant.
 *
 * A tenant has at most one subscription row (enforced by a unique index on
 * `tenant_id`); billing periods are recorded as {@see SubscriptionInvoice}
 * rows so plan changes and renewals keep their history.
 */
class Subscription extends Model
{
    /** @use HasFactory<SubscriptionFactory> */
    use HasFactory;

    protected $connection = 'central';

    protected $fillable = [
        'tenant_id',
        'plan_id',
        'plan_name',
        'billing_cycle',
        'price',
        'currency',
        'start_date',
        'end_date',
        'trial_ends_at',
        'status',
        'auto_renew',
        'last_renewed_at',
        'cancelled_at',
        'cancel_at_period_end',
        'features_json',
        'limits_json',
        'feature_overrides_json',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'trial_ends_at' => 'date',
            'last_renewed_at' => 'datetime',
            'price' => 'decimal:2',
            'features_json' => 'array',
            'limits_json' => 'array',
            'feature_overrides_json' => 'array',
            'auto_renew' => 'boolean',
            'cancel_at_period_end' => 'boolean',
            'cancelled_at' => 'datetime',
            'status' => SubscriptionStatus::class,
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(SubscriptionInvoice::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', SubscriptionStatus::ACTIVE->value);
    }

    /**
     * Subscriptions that still grant access (trial, active or past due).
     */
    public function scopeUsable($query)
    {
        return $query->whereIn('status', array_map(
            fn (SubscriptionStatus $status) => $status->value,
            SubscriptionStatus::usable(),
        ));
    }

    /**
     * Status including the effect of the end date, which is what the UI and
     * the access middleware should show.
     */
    public function effectiveStatus(): SubscriptionStatus
    {
        $status = $this->status;

        if ($status->isTerminal()) {
            return $status;
        }

        if ($this->end_date !== null && $this->end_date->isPast() && ! $this->endsToday()) {
            return $status === SubscriptionStatus::TRIALING
                ? SubscriptionStatus::EXPIRED
                : SubscriptionStatus::PAST_DUE;
        }

        return $status;
    }

    public function grantsAccess(): bool
    {
        return $this->effectiveStatus()->grantsAccess()
            && ! $this->isBeyondGrace();
    }

    public function isBeyondGrace(): bool
    {
        if ($this->end_date === null) {
            return false;
        }

        $graceDays = (int) ($this->plan?->graceDays() ?? 3);

        return $this->end_date->copy()->addDays($graceDays)->isPast();
    }

    public function endsToday(): bool
    {
        return $this->end_date !== null && $this->end_date->isToday();
    }

    public function isOnTrial(): bool
    {
        return $this->status === SubscriptionStatus::TRIALING
            && ($this->trial_ends_at === null || $this->trial_ends_at->isFuture());
    }

    public function daysRemaining(): ?int
    {
        $until = $this->isOnTrial() ? ($this->trial_ends_at ?? $this->end_date) : $this->end_date;

        if ($until === null) {
            return null;
        }

        return (int) CarbonImmutable::now()->startOfDay()->diffInDays($until->startOfDay(), false);
    }

    /**
     * Effective capability flags: plan defaults overridden per tenant.
     *
     * @return array<string, bool>
     */
    public function featureFlags(): array
    {
        $planFlags = $this->plan?->featureFlags() ?? [];

        foreach ($this->feature_overrides_json ?? [] as $feature => $enabled) {
            $planFlags[$feature] = (bool) $enabled;
        }

        return $planFlags;
    }

    public function featureEnabled(string $feature): bool
    {
        return (bool) ($this->featureFlags()[$feature] ?? false);
    }

    /**
     * Effective quotas: plan defaults overridden per tenant. `null` means
     * unlimited.
     *
     * @return array{users: int|null, patients: int|null, storage_gb: int|null}
     */
    public function limits(): array
    {
        $limits = $this->plan?->limits() ?? ['users' => null, 'patients' => null, 'storage_gb' => null];

        foreach ($this->limits_json ?? [] as $key => $value) {
            if (array_key_exists($key, $limits)) {
                $limits[$key] = $value === null ? null : (int) $value;
            }
        }

        return $limits;
    }

    public function limit(string $key): ?int
    {
        return $this->limits()[$key] ?? null;
    }

    public function priceForCurrentCycle(): float
    {
        if ($this->plan !== null) {
            return $this->plan->priceFor($this->billing_cycle);
        }

        return (float) $this->price;
    }

    /**
     * Length of the billing cycle in months.
     */
    public function cycleMonths(): int
    {
        return $this->billing_cycle === 'yearly' ? 12 : 1;
    }
}
