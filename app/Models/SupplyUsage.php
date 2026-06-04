<?php

namespace App\Models;

use Database\Factories\SupplyUsageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['supply_id', 'used_by', 'patient_id', 'used_quantity', 'used_at', 'purpose'])]
class SupplyUsage extends Model
{
    /** @use HasFactory<SupplyUsageFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'used_quantity' => 'integer',
            'used_at' => 'datetime',
        ];
    }

    public function supply(): BelongsTo
    {
        return $this->belongsTo(Supply::class);
    }

    public function usedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
