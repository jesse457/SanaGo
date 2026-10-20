<?php

namespace App\Models;

use App\Enums\SubscriptionInvoiceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * One billing period for a tenant subscription.
 */
class SubscriptionInvoice extends Model
{
    use HasFactory;

    protected $connection = 'central';

    protected $fillable = [
        'tenant_id',
        'subscription_id',
        'number',
        'currency',
        'subtotal',
        'tax',
        'total',
        'status',
        'period_start',
        'period_end',
        'issued_at',
        'due_at',
        'paid_at',
        'payment_method',
        'reference',
        'notes',
        'meta_json',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'status' => SubscriptionInvoiceStatus::class,
            'period_start' => 'date',
            'period_end' => 'date',
            'issued_at' => 'datetime',
            'due_at' => 'datetime',
            'paid_at' => 'datetime',
            'meta_json' => 'array',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Human-facing invoice number, e.g. SNG-2026-000042.
     */
    public static function generateNumber(): string
    {
        $sequence = (int) static::query()->whereYear('created_at', now()->year)->count() + 1;

        do {
            $number = sprintf('SNG-%s-%06d', now()->format('Y'), $sequence);
            $sequence++;
        } while (static::query()->where('number', $number)->exists());

        return $number;
    }

    public function markPaid(?string $method = null, ?string $reference = null): void
    {
        $this->update([
            'status' => SubscriptionInvoiceStatus::PAID,
            'paid_at' => now(),
            'payment_method' => $method ?? $this->payment_method ?? 'cash',
            'reference' => $reference ?? $this->reference ?? Str::upper(Str::random(8)),
        ]);
    }

    public function scopeOutstanding($query)
    {
        return $query->whereIn('status', [
            SubscriptionInvoiceStatus::PENDING->value,
            SubscriptionInvoiceStatus::OVERDUE->value,
        ]);
    }
}
