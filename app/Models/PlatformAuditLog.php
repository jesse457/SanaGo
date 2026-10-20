<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Central audit trail for landlord / platform actions.
 */
class PlatformAuditLog extends Model
{
    protected $connection = 'central';

    protected $fillable = [
        'actor_id',
        'actor_name',
        'actor_email',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'tenant_id',
        'properties_json',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties_json' => 'array',
        'actor_id' => 'integer',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function scopeForTenant($query, string $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}
