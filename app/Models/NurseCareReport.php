<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class NurseCareReport extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'patient_id',
        'user_id',
        'report_time',
        'shift_type',
        'interventions',
        'observations',
    ];

    protected $casts = [
        'report_time' => 'datetime',
    ];

    /**
     * Relationship: The patient this report belongs to.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relationship: The nurse (user) who created this report.
     */
    public function nurse()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
