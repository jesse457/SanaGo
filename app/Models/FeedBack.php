<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class FeedBack extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'subject',
        'category',
        'message',
        'response',
        'response_draft',
        'user_id',
        'status',
    ];

    // helper: has a published response
    public function hasResponse(): bool
    {
        return ! empty($this->response);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // helper: formatted response or fallback
    public function getResponseOrFallbackAttribute()
    {
        return $this->response ?: null;
    }
}
