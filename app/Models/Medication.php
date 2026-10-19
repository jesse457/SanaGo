<?php

namespace App\Models;

use Database\Factories\MedicationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['name', 'strength', 'dosage_units', 'min_stock_level', 'total_stock', 'description', 'unit_cost', 'is_active', 'service_id'])]
class Medication extends Model implements HasMedia
{
    /** @use HasFactory<MedicationFactory> */
    use HasFactory, InteractsWithMedia;

    protected function casts(): array
    {
        return [
            'min_stock_level' => 'integer',
            'total_stock' => 'integer',
            'unit_cost' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    protected $appends = ['image_url'];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['media'];

    public function getImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('image') ?: null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }

    public function batches(): MorphMany
    {
        return $this->morphMany(Batch::class, 'batchable');
    }

    public function prescriptionItems(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
