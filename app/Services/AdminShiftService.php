<?php

namespace App\Services;

use App\Models\UserActivity;
use App\Models\UserShift;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminShiftService
{
    /**
     * Private helper to log user activity
     */
    private function logActivity(string $type, string $description): void
    {
        UserActivity::create([
            'user_id' => Auth::id(),
            'activity_type' => $type, // 'created', 'updated', 'deleted'
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get paginated shifts with staff counts and user details.
     */
    public function getPaginatedShifts(int $perPage = 10): LengthAwarePaginator
    {
        $tenantId = tenant('id');
        $page = request()->get('page', 1);

        return Cache::remember("admin_shifts_{$tenantId}_p{$page}", 300, function () use ($perPage) {
            return UserShift::query()
                ->with(['user:id,name,profile_picture'])
                ->withCount('user')
                ->orderBy('shift_date', 'desc')
                ->orderBy('start_time', 'asc')
                ->paginate($perPage);
        });
    }

    /**
     * Store or Update a shift.
     */
    public function saveShift(array $data, ?int $id = null): UserShift
    {
        return DB::connection('pgsql_transaction')->transaction(function () use ($data, $id) {
            $isNew = $id === null;

            $shift = UserShift::updateOrCreate(
                ['id' => $id],
                [
                    'shift_type' => $data['shift_type'],
                    'shift_date' => $data['shift_date'],
                    'start_time' => $data['start_time'],
                    'end_time' => $data['end_time'],
                ]
            );

            $activityType = $isNew ? 'created' : 'updated';
            $description = ($isNew ? 'Created' : 'Updated')." shift schedule: {$shift->shift_type} on {$shift->shift_date}";

            $this->logActivity($activityType, $description);
            $this->clearCache();

            return $shift;
        });
    }

    /**
     * Delete a shift.
     */
    public function deleteShift(int $id): bool
    {
        return DB::connection('pgsql_transaction')->transaction(function () use ($id) {
            $shift = UserShift::findOrFail($id);
            $description = "Deleted shift schedule: {$shift->shift_type} for {$shift->shift_date}";

            $deleted = $shift->delete();

            if ($deleted) {
                $this->logActivity('deleted', $description);
                $this->clearCache();
            }

            return $deleted;
        });
    }

    /**
     * Clear shift-related cache for the tenant.
     */
    private function clearCache(): void
    {
        $tenantId = tenant('id');
        for ($i = 1; $i <= 10; $i++) {
            Cache::forget("admin_shifts_{$tenantId}_p{$i}");
        }
    }
}
