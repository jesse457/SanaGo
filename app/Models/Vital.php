<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Vital extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'patient_id',
        'medical_record_id',
        'nurse_id',
        'recorded_at',
        'tenant_id',
        'temperature_celsius',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'heart_rate_bpm',
        'respiratory_rate',
        'spo2_percentage',
        'weight_kg',
        'bmi',
        'flag_abnormal',
        'notes',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'flag_abnormal' => 'boolean',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function nurse()
    {
        return $this->belongsTo(User::class, 'nurse_id');
    }
}
