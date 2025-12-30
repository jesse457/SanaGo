<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class DemoRequest extends Model
{
    use HasFactory,CentralConnection;

    protected $fillable = [
        'full_name',
        'email',
        'phone_number',
        'has_whatsapp',
        'facility_name',
        'facility_type',
        'region',
        'job_title',
        'status'
    ];
}
