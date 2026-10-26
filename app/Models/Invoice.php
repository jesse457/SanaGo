<?php

namespace App\Models;

use App\Enums\ClaimStatus;
use App\Enums\InvoiceStatus;
use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['invoice_number', 'patient_id', 'invoice_date', 'due_date', 'subtotal', 'tax_amount', 'total_amount', 'status', 'paid_at', 'notes'])]
class Invoice extends Model
{
    /** @use HasFactory<InvoiceFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date:Y-m-d',
            'due_date' => 'date:Y-m-d',
            'paid_at' => 'date:Y-m-d',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'status' => InvoiceStatus::class,
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function claim(): HasOne
    {
        return $this->hasOne(Claim::class);
    }

    public function cashPayments(): HasMany
    {
        return $this->hasMany(CashPayment::class);
    }

    /**
     * Total amount paid (cash payments + paid insurance claims).
     */
    public function getTotalPaidAttribute(): float
    {
        $cashPaid = $this->relationLoaded('cashPayments')
            ? (float) $this->cashPayments->sum('amount_paid')
            : (float) $this->cashPayments()->sum('amount_paid');

        if ($this->relationLoaded('claim')) {
            $claimPaid = $this->claim?->status === ClaimStatus::Paid
                ? (float) $this->claim->claim_amount
                : 0.0;
        } else {
            $claimPaid = (float) $this->claim()->where('status', ClaimStatus::Paid->value)->sum('claim_amount');
        }

        return $cashPaid + $claimPaid;
    }

    /**
     * Remaining balance to be paid.
     */
    public function getRemainingBalanceAttribute(): float
    {
        return max(0, (float) $this->total_amount - $this->total_paid);
    }

    /**
     * Whether the invoice is fully paid.
     */
    public function getIsFullyPaidAttribute(): bool
    {
        return $this->remaining_balance <= 0;
    }

    /**
     * Appended accessors.
     */
    protected $appends = ['total_paid', 'remaining_balance', 'is_fully_paid'];
}
