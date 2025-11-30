<?php

namespace App\Models;

use App\Traits\TracksRevenue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Admission extends Model
{
    use BelongsToTenant, HasFactory,TracksRevenue;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'bed_id',
        'admission_date',
        'observation_fee',
        'discharge_date',
        'reason_for_admission',
        'status',
    ];

    protected $casts = [
        'admission_date' => 'datetime',
        'discharge_date' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function bed()
    {
        return $this->belongsTo(Bed::class);
    }
}
