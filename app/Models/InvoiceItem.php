<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['invoice_id', 'item_type', 'item_id', 'description', 'quantity', 'unit_price', 'total_price', 'settled_at'])]
class InvoiceItem extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'settled_at' => 'datetime',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Whether the service on this line has been paid for at a counter.
     */
    public function isSettled(): bool
    {
        return $this->settled_at !== null;
    }

    /**
     * Unpaid lines of a patient: what the cashier still has to collect.
     *
     * @param  Builder<self>  $query
     */
    public function scopeUnsettled(Builder $query): void
    {
        $query->whereNull('settled_at');
    }
}
