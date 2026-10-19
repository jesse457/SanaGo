<?php

namespace App\Models;

use App\Enums\CashShiftStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'payment_point', 'opening_balance',
        'closing_balance', 'expected_total', 'variance',
        'status', 'opened_at', 'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:2',
            'closing_balance' => 'decimal:2',
            'expected_total' => 'decimal:2',
            'variance' => 'decimal:2',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
            'status' => CashShiftStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CashPayment::class);
    }
}
