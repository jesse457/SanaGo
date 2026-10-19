<?php

namespace App\Models;

use Database\Factories\BedTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description', 'capacity', 'daily_rate', 'service_id'])]
class BedType extends Model
{
    /** @use HasFactory<BedTypeFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'daily_rate' => 'decimal:2',
        ];
    }

    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
