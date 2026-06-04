<?php

namespace App\Models;

use Database\Factories\VitalFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['patient_id', 'recorded_by', 'recorded_at', 'temperature', 'systolic_bp', 'diastolic_bp', 'heart_rate', 'oxygen_saturation', 'respiratory_rate', 'is_abnormal', 'notes'])]
class Vital extends Model
{
    /** @use HasFactory<VitalFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'recorded_at' => 'datetime',
            'temperature' => 'decimal:1',
            'systolic_bp' => 'integer',
            'diastolic_bp' => 'integer',
            'heart_rate' => 'integer',
            'oxygen_saturation' => 'integer',
            'respiratory_rate' => 'integer',
            'is_abnormal' => 'boolean',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
