<?php

namespace App\Models;

use App\Traits\TracksRevenue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Appointment extends Model
{
    use BelongsToTenant, HasFactory, TracksRevenue;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'tenant_id',
        'appointment_date',
        'appointment_time', // Represents check-in/scheduled arrival time
        'price',
        'reason_for_visit',
        'status', // New statuses: Waiting, In Consultation, Completed, Canceled
        'notes',
        'queue_position',
        'actual_start_time',
        'actual_end_time',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'datetime',
        'actual_start_time' => 'datetime',
        'actual_end_time' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    // New scope to find patients waiting on a specific day for a doctor
    public function scopeInQueueForDoctor($query, $doctorId, $date)
    {
        return $query->where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->whereIn('status', ['Waiting', 'In Consultation']);
    }

    // Other helper methods can be added here
    public function startConsultation()
    {
        $this->status = 'In Consultation';
        $this->actual_start_time = now();
        $this->save();
    }

    public function endConsultation()
    {
        $this->status = 'Completed';
        $this->actual_end_time = now();
        $this->save();
    }

    public function cancel()
    {
        $this->status = 'Canceled';
        $this->save();
    }
}
