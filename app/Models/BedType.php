<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class BedType extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price_per_day',
    ];

    public function beds()
    {
        return $this->hasMany(Bed::class);
    }
}
