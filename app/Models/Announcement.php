<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $connection = 'central';

    protected $fillable = [
        'title',
        'body',
        'type',
        'is_published',
        'published_at',
        'expires_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];
}
