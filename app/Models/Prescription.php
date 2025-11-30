<?php

namespace App\Models;

use App\Traits\TracksRevenue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Prescription extends Model
{
    use BelongsToTenant, HasFactory,TracksRevenue;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'consultation_id',
        'prescription_date',
        'general_notes',
        'status',
    ];

    protected $casts = [
        'prescription_date' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function consultation()
    {
        return $this->belongsTo(MedicalRecord::class, 'consultation_id');
    }

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }
}
