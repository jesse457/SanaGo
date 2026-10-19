<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Note extends Model
{
    protected $connection = 'central';

    protected $fillable = [
        'notable_type',
        'notable_id',
        'user_id',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'notable_id' => 'integer',
            'user_id' => 'integer',
        ];
    }

    public function notable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
