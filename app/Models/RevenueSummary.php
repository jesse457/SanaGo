<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class RevenueSummary extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'patient_id',
        'transaction_date',
        'medication_revenue',
        'appointment_revenue',
        'lab_revenue',
        'bed_fee_revenue',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
