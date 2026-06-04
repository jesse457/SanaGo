<?php

namespace App\Models;

use App\Enums\BedStatus;
use Database\Factories\BedFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['ward_id', 'code', 'bed_type_id', 'status'])]
class Bed extends Model
{
    /** @use HasFactory<BedFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => BedStatus::class,
        ];
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    public function bedType(): BelongsTo
    {
        return $this->belongsTo(BedType::class);
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }
}
