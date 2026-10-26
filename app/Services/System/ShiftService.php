<?php

namespace App\Services\System;

use App\Models\UserShift;
use Illuminate\Support\Facades\DB;

class ShiftService
{
    public function create(array $data): UserShift
    {
        return DB::transaction(function () use ($data) {
            $shift = UserShift::create($this->shiftAttributes($data));
            $this->syncStaffing($shift, $data['staff'] ?? []);

            return $shift;
        });
    }

    public function update(UserShift $shift, array $data): UserShift
    {
        return DB::transaction(function () use ($shift, $data) {
            $shift->update($this->shiftAttributes($data));
            $this->syncStaffing($shift, $data['staff'] ?? []);

            return $shift->fresh();
        });
    }

    public function delete(UserShift $shift): void
    {
        $shift->delete();
    }

    public function getShiftsByDate(string $date)
    {
        return UserShift::with('staffAssignments.user.department')
            ->where('shift_date', $date)
            ->orderBy('shift_type')
            ->get();
    }

    public function getRoster(string $startDate, string $endDate)
    {
        return UserShift::with('staffAssignments.user.department')
            ->whereBetween('shift_date', [$startDate, $endDate])
            ->orderBy('shift_date')
            ->orderBy('shift_type')
            ->get();
    }

    /**
     * The submitted staffing replaces the stored one: entries carry either a
     * system user id or a free-text name for staff who are not in the system.
     *
     * @param  array<int, array{user_id?: int|string|null, name?: string|null}>  $staff
     */
    private function syncStaffing(UserShift $shift, array $staff): void
    {
        $users = collect($staff)
            ->pluck('user_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->map(fn (int $id) => ['user_id' => $id]);

        $external = collect($staff)
            ->pluck('name')
            ->map(fn ($name) => is_string($name) ? trim($name) : null)
            ->filter()
            ->unique()
            ->values()
            ->map(fn (string $name) => ['name' => $name]);

        $shift->staffAssignments()->delete();
        $shift->staffAssignments()->createMany($users->concat($external)->all());
    }

    /**
     * @return array{shift_date: string, shift_type: string, start_time: string, end_time: string}
     */
    private function shiftAttributes(array $data): array
    {
        return collect($data)->only(['shift_date', 'shift_type', 'start_time', 'end_time'])->all();
    }
}
