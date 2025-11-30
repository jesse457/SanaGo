<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Bed extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'ward_id',
            'tenant_id',
        'bed_type_id',
        'bed_number',
        'is_occupied',
    ];

    protected $casts = [
        'is_occupied' => 'boolean',
    ];

    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    public function bedType()
    {
        return $this->belongsTo(BedType::class);
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }
}
