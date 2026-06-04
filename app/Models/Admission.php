<?php

namespace App\Models;

use App\Enums\AdmissionStatus;
use Database\Factories\AdmissionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable(['patient_id', 'bed_id', 'attending_doctor_id', 'admission_date', 'discharge_date', 'status', 'reason_for_admission', 'observation_fee'])]
class Admission extends Model
{
    /** @use HasFactory<AdmissionFactory> */
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    protected function casts(): array
    {
        return [
            'admission_date' => 'date:Y-m-d',
            'discharge_date' => 'date:Y-m-d',
            'observation_fee' => 'decimal:2',
            'status' => AdmissionStatus::class,
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class);
    }

    public function attendingDoctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'attending_doctor_id');
    }
}
