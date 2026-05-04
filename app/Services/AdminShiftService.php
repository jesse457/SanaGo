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
        // Wrap in try-catch to ensure logging failure doesn't stop the request
        try {
            UserActivity::create([
                'user_id' => Auth::id(),
                'activity_type' => $type,
                'description' => $description,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Quietly fail logging if necessary
        }
    }

    /**
     * Get paginated shifts.
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
     *
     * @param array $data Validated shift data
     * @param int|null $id If provided, updates existing record. If null, creates new.
     */
    public function saveShift(array $data, ?int $id = null): UserShift
    {
        return DB::transaction(function () use ($data, $id) {
            $isNew = $id === null;

            if ($isNew) {
                $shift = new UserShift();
                $actionText = 'Created';
                $activityType = 'created';
            } else {
                $shift = UserShift::findOrFail($id);
                $actionText = 'Updated';
                $activityType = 'updated';
            }

            // Assign attributes
            $shift->shift_type = $data['shift_type'];
            $shift->shift_date = $data['shift_date'];
            $shift->start_time = $data['start_time'];
            $shift->end_time = $data['end_time'];
            $shift->save();

            $description = "{$actionText} shift schedule: {$shift->shift_type} on {$shift->shift_date}";

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
        return DB::transaction(function () use ($id) {
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
        // Increased loop limit to ensure deeper pages are cleared
        for ($i = 1; $i <= 50; $i++) {
            Cache::forget("admin_shifts_{$tenantId}_p{$i}");
        }
    }
}
