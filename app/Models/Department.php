<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Department extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function wards()
    {
        return $this->hasMany(Ward::class);
    }
}
