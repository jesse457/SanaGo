<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class PrescriptionItem extends Model
{
    use HasFactory,BelongsToTenant; // No BelongsToTenant directly as it's through Prescription

    protected $fillable = [
        'prescription_id',
        'medication_id',
        'dosage',
        'frequency',
        'duration',
        'quantity_prescribed',
        'dispensed_quantity',
        'notes',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }

    public function dispensations()
    {
        return $this->hasMany(Dispensation::class);
    }
}
