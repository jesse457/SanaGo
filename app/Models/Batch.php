<?php

namespace App\Models;

use App\Enums\BatchStatus;
use Database\Factories\BatchFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['batchable_id', 'batchable_type', 'batch_number', 'expiry_date', 'initial_quantity', 'current_quantity', 'purchase_price', 'purchase_date', 'supplier', 'status', 'notes'])]
class Batch extends Model
{
    /** @use HasFactory<BatchFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'expiry_date' => 'date:Y-m-d',
            'purchase_date' => 'date:Y-m-d',
            'purchase_price' => 'decimal:2',
            'initial_quantity' => 'integer',
            'current_quantity' => 'integer',
            'status' => BatchStatus::class,
        ];
    }

    public function batchable(): MorphTo
    {
        return $this->morphTo();
    }
}
