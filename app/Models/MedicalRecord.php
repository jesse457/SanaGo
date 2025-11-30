<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class MedicalRecord extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'complaint',
        'general_notes',
        'diagnosis_text',
        'treatment_plan',
        'finalized',
        'record_type',
    ];

    protected $casts = [
        'finalized' => 'boolean',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function prescription()
    {
        return $this->hasMany(Prescription::class, 'consultation_id');
    }

    public function labResults()
    {
        return $this->hasMany(LabResult::class, 'consultation_id');
    }

    public function attachments()
    {
        return $this->hasMany(MedicalRecordAttachment::class);
    }

    public function vitals()
    {
        return $this->hasMany(Vital::class);
    }
}
