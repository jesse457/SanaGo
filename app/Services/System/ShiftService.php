<?php

namespace App\Services\System;

use App\Models\UserShift;
use Illuminate\Support\Facades\DB;

class ShiftService
{
    public function create(array $data): UserShift
    {
        return DB::transaction(fn () => UserShift::create($data));
    }

    public function update(UserShift $shift, array $data): UserShift
    {
        $shift->update($data);

        return $shift->fresh();
    }

    public function delete(UserShift $shift): void
    {
        $shift->delete();
    }

    public function getShiftsByDate(string $date)
    {
        return UserShift::with('user.department')
            ->where('shift_date', $date)
            ->orderBy('shift_type')
            ->get();
    }

    public function getRoster(string $startDate, string $endDate)
    {
        return UserShift::with('user.department')
            ->whereBetween('shift_date', [$startDate, $endDate])
            ->orderBy('shift_date')
            ->orderBy('shift_type')
            ->get();
    }
}
