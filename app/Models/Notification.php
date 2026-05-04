<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification; // Extend the native class
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Notification extends DatabaseNotification
{
    use BelongsToTenant, HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notifications';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', // ID must be fillable because Laravel generates the UUID before saving
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
        'tenant_id', // Required for BelongsToTenant
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'id' => 'string',
    ];

    // ==========================================
    // Custom Helper Methods
    // ==========================================

    /**
     * Get the notification message from the data array.
     */
    public function getMessageAttribute(): string
    {
        return $this->data['message'] ?? 'New Notification';
    }

    /**
     * Get the notification type/category from the data array.
     */
    public function getNotificationTypeAttribute(): string
    {
        return $this->data['type'] ?? 'general';
    }

    /**
     * Get the URL/Action link from the data array.
     */
    public function getLinkAttribute(): ?string
    {
        return $this->data['link'] ?? null;
    }

    // ==========================================
    // Scope Overrides (Fixed Logic)
    // ==========================================

    /**
     * Scope a query to only include unread notifications.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnread($query)
    {
        // FIXED: Standard Laravel uses null check on read_at, not a boolean column
        return $query->whereNull('read_at');
    }

    /**
     * Scope a query to only include read notifications.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRead($query)
    {
        // FIXED: Check where read_at is NOT null
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope a query to only include notifications of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
