<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class SupplyUsage extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'supply_id',
         'tenant_id',
        'user_id',
        'patient_id',
        'quantity_used',
        'usage_date',
    ];

    protected $casts = [
        'usage_date' => 'datetime',
    ];

    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
