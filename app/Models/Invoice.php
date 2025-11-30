<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Invoice extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'patient_id',
        'amount',
        'status',
        'payment_method',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
