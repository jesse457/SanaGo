<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase;
    use HasDomains;
    use HasFactory;

    protected $connection = 'central';

    protected $casts = [
        'is_active' => 'boolean',
        'suspended_at' => 'datetime',
        'trial_ends_at' => 'date',
        'archived_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'settings_json' => 'array',
    ];

    /**
     * NOTE: do not add a `name` accessor here.
     *
     * stancl/tenancy stores non-column attributes (name, tenancy_db_name…) in
     * the `data` JSON blob and hydrates them through VirtualColumn. Defining
     * `getNameAttribute()` shadows that lookup — `$this->getAttribute('data')`
     * is null by the time the accessor runs, so every tenant name silently
     * degraded to its id. Use {@see displayName()} where a fallback is needed.
     */
    public function displayName(): string
    {
        $name = $this->getAttribute('name');

        return is_string($name) && trim($name) !== '' ? $name : (string) $this->getTenantKey();
    }

    public function getTenantKeyName(): string
    {
        return 'id';
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class, 'tenant_id', 'id');
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class, 'tenant_id', 'id')
            ->whereIn('status', array_map(
                fn (SubscriptionStatus $status) => $status->value,
                SubscriptionStatus::usable(),
            ));
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(SubscriptionInvoice::class, 'tenant_id', 'id');
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'notable');
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'tenant_id', 'id');
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active && ! $this->isArchived();
    }

    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }

    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }

    public function isOnTrial(): bool
    {
        return $this->trial_ends_at !== null && $this->trial_ends_at->isFuture();
    }

    public function primaryDomain(): ?string
    {
        return $this->domains->first()?->domain;
    }

    public function planName(): ?string
    {
        return $this->subscription?->plan_name;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->whereNull('archived_at');
    }

    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }
}
