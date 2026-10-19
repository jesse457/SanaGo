<?php

namespace App\Models;

use App\Enums\SurgeryStatus;
use App\Enums\UrgencyLevel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['patient_id', 'surgeon_id', 'surgeon_name', 'anesthetist_id', 'anesthetist_name', 'bed_id', 'surgery_name', 'description', 'scheduled_at', 'completed_at', 'estimated_duration_minutes', 'urgency', 'status', 'surgery_fee', 'notes', 'complications'])]
class Surgery extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'completed_at' => 'datetime',
            'surgery_fee' => 'decimal:2',
            'urgency' => UrgencyLevel::class,
            'status' => SurgeryStatus::class,
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function surgeon(): BelongsTo
    {
        return $this->belongsTo(User::class, 'surgeon_id');
    }

    public function anesthetist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'anesthetist_id');
    }

    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class);
    }

    /**
     * Get the display name for the surgeon (internal user or external name).
     */
    public function getSurgeonDisplayAttribute(): string
    {
        if ($this->surgeon) {
            return $this->surgeon->name;
        }

        return $this->surgeon_name ?? 'N/A';
    }

    /**
     * Get the display name for the anesthetist (internal user or external name).
     */
    public function getAnesthetistDisplayAttribute(): string
    {
        if ($this->anesthetist) {
            return $this->anesthetist->name;
        }

        return $this->anesthetist_name ?? 'N/A';
    }
}
