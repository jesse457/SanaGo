<?php

namespace App\Models;

use App\Enums\RadiologyStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class RadiologyResult extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

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

    /**
     * Imaging plates and the signed report PDFs that go with them.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('report')
            ->acceptsMimeTypes([
                'application/pdf',
                'image/jpeg',
                'image/png',
                'image/webp',
            ]);
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
