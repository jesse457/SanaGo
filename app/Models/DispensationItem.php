<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['dispensation_id', 'medication_id', 'quantity', 'unit_price'])]
class DispensationItem extends Model
{
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
        ];
    }

    public function dispensation(): BelongsTo
    {
        return $this->belongsTo(Dispensation::class);
    }

    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }
}
