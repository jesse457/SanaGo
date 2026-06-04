<?php

namespace App\Models;

use Database\Factories\ProcedureKitFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable(['name', 'description', 'current_stock', 'min_stock_level', 'unit_cost', 'category', 'contents'])]
class ProcedureKit extends Model
{
    /** @use HasFactory<ProcedureKitFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'current_stock' => 'integer',
            'min_stock_level' => 'integer',
            'unit_cost' => 'decimal:2',
            'contents' => 'array',
        ];
    }

    public function batches(): MorphMany
    {
        return $this->morphMany(Batch::class, 'batchable');
    }
}
