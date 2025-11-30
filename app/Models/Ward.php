<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Ward extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'name',
        'tenant_id',
        'ward_number',
        'department_id',
        'description',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function beds()
    {
        return $this->hasMany(Bed::class);
    }
}
