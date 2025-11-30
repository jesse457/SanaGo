<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class UserShift extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'shift_type',
        'start_time',
        'end_time',
        'shift_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'shift_type' => 'string', // <-- THIS IS THE FIX
        'shift_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function user()
    {
        return $this->belongsToMany(User::class);
    }
}
