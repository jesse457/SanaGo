<?php

namespace App\Models;

use Database\Factories\NurseCareReportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['patient_id', 'nurse_id', 'report_date', 'shift_type', 'care_details', 'observations'])]
class NurseCareReport extends Model
{
    /** @use HasFactory<NurseCareReportFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'report_date' => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nurse_id');
    }
}
