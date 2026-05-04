<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use ParagonIE\CipherSweet\EncryptedRow;
use Spatie\LaravelCipherSweet\Concerns\UsesCipherSweet;
use Spatie\LaravelCipherSweet\Contracts\CipherSweetEncrypted;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class UserActivity extends Model implements CipherSweetEncrypted
{
    use BelongsToTenant, HasFactory, UsesCipherSweet;

    protected $fillable = [
        'user_id',
        'activity_type',
        'description',
        'ip_address',
        'tenant_id',
        'user_agent',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
        // Ensure timestamps are properly cast
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Configure CipherSweet encryption.
     * Only encrypt fields that contain sensitive text data.
     */
    public static function configureCipherSweet(EncryptedRow $encryptedRow): void
    {
        $encryptedRow->addField('description');
        // Add more fields as needed, e.g.:
        // ->addField('ip_address')
        // ->addField('user_agent');
    }

    /**
     * Get the user that performed the activity.
     * Null-safe: relationship may return null if user_id is null or user was deleted.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault(function ($user) {
            // Provide safe defaults if user relation is missing
            $user->id = null;
            $user->name = 'Deleted User';
            $user->email = 'unknown@example.com';
            $user->profile_picture = null;
        });
    }

    /**
     * Null-safe accessor for activity type.
     */
    public function getActivityTypeAttribute(?string $value): string
    {
        return $value ?? 'unknown';
    }

    /**
     * Null-safe accessor for description.
     */
    public function getDescriptionAttribute(?string $value): string
    {
        return $value ?? '';
    }

    /**
     * Null-safe accessor for IP address.
     */
    public function getIpAddressAttribute(?string $value): ?string
    {
        return $value;
    }
}
