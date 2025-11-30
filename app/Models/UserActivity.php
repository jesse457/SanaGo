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
    ];

    /**
     * Configure CipherSweet encryption for this model.
     *
     * Fields added with ->addField(...) should correspond to text columns
     * in your DB to store ciphertext. For exact-match searchable fields,
     * add a BlindIndex.
     */
    public static function configureCipherSweet(EncryptedRow $encryptedRow): void
    {
        $encryptedRow

            ->addField('description');

        // If you want case-insensitive or transformed blind indexes, define
        // transformations via BlindIndex options (see CipherSweet docs).
    }

    /**
     * Get the user that performed the activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
