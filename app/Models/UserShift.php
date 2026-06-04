<?php

namespace App\Models;

use App\Enums\ShiftType;
use Database\Factories\UserShiftFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'shift_date', 'shift_type', 'start_time', 'end_time'])]
class UserShift extends Model
{
    /** @use HasFactory<UserShiftFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'shift_date' => 'date:Y-m-d',
            'start_time' => 'string',
            'end_time' => 'string',
            'shift_type' => ShiftType::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
