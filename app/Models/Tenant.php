<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SubscriptionStatus;
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

    protected $connection = 'central';

    protected $casts = [
        'is_active' => 'boolean',
        'suspended_at' => 'datetime',
        'trial_ends_at' => 'date',
        'settings_json' => 'array',
    ];

    public function getTenantKeyName(): string
    {
        return 'id';
    }

    public function getNameAttribute(): string
    {
        $data = $this->getAttribute('data');

        return is_array($data) ? ($data['name'] ?? $this->id) : $this->id;
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class, 'tenant_id', 'id');
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class, 'tenant_id', 'id')
            ->where('status', SubscriptionStatus::ACTIVE);
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
        return (bool) $this->is_active;
    }

    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }

    public function isOnTrial(): bool
    {
        return $this->trial_ends_at !== null && $this->trial_ends_at->isFuture();
    }
}
