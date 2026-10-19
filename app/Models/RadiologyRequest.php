<?php

namespace App\Models;

use App\Enums\RadiologyStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RadiologyRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'consultation_id', 'requested_by',
        'imaging_type', 'body_part', 'clinical_notes', 'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => RadiologyStatus::class,
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class, 'consultation_id');
    }

    public function result(): HasOne
    {
        return $this->hasOne(RadiologyResult::class, 'request_id');
    }
}
