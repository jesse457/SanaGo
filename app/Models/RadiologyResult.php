<?php

namespace App\Models;

use App\Enums\RadiologyStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RadiologyResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id', 'performed_by', 'findings', 'conclusion',
        'validated_by', 'validated_at', 'status',
    ];

    protected function casts(): array
    {
        return [
            'validated_at' => 'datetime',
            'status' => RadiologyStatus::class,
        ];
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(RadiologyRequest::class, 'request_id');
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
