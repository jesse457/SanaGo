<?php

namespace App\Models;

use App\Enums\EmergencyStatus;
use App\Enums\UrgencyLevel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['patient_id', 'attending_doctor_id', 'triage_nurse_id', 'arrival_mode', 'chief_complaint', 'primary_assessment', 'triage_level', 'status', 'disposition', 'admission_id', 'arrived_at', 'triaged_at', 'stabilized_at', 'discharged_at', 'emergency_fee', 'treatment_notes', 'discharge_instructions'])]
class EmergencyCase extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'arrived_at' => 'datetime',
            'triaged_at' => 'datetime',
            'stabilized_at' => 'datetime',
            'discharged_at' => 'datetime',
            'emergency_fee' => 'decimal:2',
            'triage_level' => UrgencyLevel::class,
            'status' => EmergencyStatus::class,
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function attendingDoctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'attending_doctor_id');
    }

    public function triageNurse(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triage_nurse_id');
    }

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }
}
