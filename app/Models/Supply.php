<?php

namespace App\Models;

use Database\Factories\SupplyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['name', 'unit_of_measure', 'current_stock', 'min_stock_level', 'cost_per_unit', 'category', 'last_restocked'])]
class Supply extends Model implements HasMedia
{
    /** @use HasFactory<SupplyFactory> */
    use HasFactory, InteractsWithMedia;

    protected $appends = ['image_url'];

    protected function casts(): array
    {
        return [
            'current_stock' => 'integer',
            'min_stock_level' => 'integer',
            'cost_per_unit' => 'decimal:2',
            'last_restocked' => 'date',

        ];
    }

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

    public function supplyUsages(): HasMany
    {
        return $this->hasMany(SupplyUsage::class);
    }
}
