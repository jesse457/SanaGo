<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Platform-wide setting stored as a JSON value on the central connection.
 */
class PlatformSetting extends Model
{
    protected $connection = 'central';

    protected $fillable = ['key', 'value'];

    protected $casts = [
        'value' => 'json',
    ];
}
