<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserShiftStaff extends Model
{
    protected $fillable = ['user_shift_id', 'user_id', 'name'];

    protected function casts(): array
    {
        return [
            'user_shift_id' => 'integer',
            'user_id' => 'integer',
        ];
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(UserShift::class, 'user_shift_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
