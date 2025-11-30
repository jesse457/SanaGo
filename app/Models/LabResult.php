<?php

namespace App\Models;

use App\Traits\TracksRevenue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;


class LabResult extends Model
{
    use BelongsToTenant,HasFactory,TracksRevenue;

    protected $fillable = [

        'lab_request_id',
        'lab_technician_id',
        'doctor_id',
        'consultation_id',
        'result_date',
        'results_text',
        'analysis_comments',
        'status',
        'price'
    ];

    protected $casts = [
        'result_date' => 'datetime',
    ];

    public function labRequest()
    {
        return $this->belongsTo(LabRequest::class);
    }

    public function labTechnician()
    {
        return $this->belongsTo(User::class, 'lab_technician_id');
    }
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
    public function consultation()
    {
        return $this->belongsTo(MedicalRecord::class, 'consultation_id');
    }

    public function attachments()
    {
        return $this->hasMany(LabResultAttachment::class);
    }
}
