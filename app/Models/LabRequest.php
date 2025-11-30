<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class LabRequest extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'patient_id',
        'requested_by_doctor_id',
        'lab_test_definition_id',
        'lab_tech_id',
        'consultation_id',
        'reason_for_test',
        'urgency_level',
        'request_date',
        'status',
    ];

    protected $casts = [
        'request_date' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function labTechnician()
    {
        return $this->belongsTo(User::class, 'lab_tech_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'requested_by_doctor_id');
    }

    public function consultation()
    {
        return $this->belongsTo(MedicalRecord::class, 'consultation_id');
    }

    public function testDefinition()
    {
        return $this->belongsTo(LabTestDefinition::class, 'lab_test_definition_id');
    }

    public function result()
    {
        return $this->hasOne(LabResult::class);
    }
}
