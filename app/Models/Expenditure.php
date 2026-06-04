<?php

namespace App\Models;

use App\Enums\ExpenditureCategory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['category', 'description', 'amount', 'expenditure_date', 'recorded_by', 'vendor', 'reference_number', 'notes'])]
class Expenditure extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expenditure_date' => 'date',
            'category' => ExpenditureCategory::class,
        ];
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
