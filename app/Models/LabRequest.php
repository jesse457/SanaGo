<?php

namespace App\Models;

use App\Enums\LabRequestStatus;
use Database\Factories\LabRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['patient_id', 'test_definition_id', 'doctor_id', 'consultation_id', 'request_date', 'status', 'reason_for_test', 'urgency'])]
class LabRequest extends Model
{
    /** @use HasFactory<LabRequestFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'request_date' => 'datetime',
            'status' => LabRequestStatus::class,
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function testDefinition(): BelongsTo
    {
        return $this->belongsTo(LabTestDefinition::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function labResult(): HasOne
    {
        return $this->hasOne(LabResult::class);
    }
}
