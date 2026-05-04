<?php

namespace App\Http\Controllers\Api\Tenants\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserActivity;
use Illuminate\Http\Request;

class UserActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = UserActivity::with('user:id,name,email,profile_picture')
            ->orderByDesc('created_at');

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('description', 'ILIKE', "%{$request->search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'ILIKE', "%{$request->search}%"));
            });
        }

        // Filters
        if ($request->type) {
            $query->where('activity_type', $request->type);
        }
        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        $activities = $query->paginate(20);

        // Transform for React UI compatibility
        $activities->getCollection()->transform(function ($activity) {
            return [
                'id' => $activity->id,
                'user' => [
                    'name' => $activity->user?->name ?? 'System',
                    'email' => $activity->user?->email ?? 'N/A',
                    'initials' => strtoupper(substr($activity->user?->name ?? 'S', 0, 2)),
                ],
                'type' => $activity->activity_type,
                'description' => $activity->description,
                'time' => $activity->created_at->format('d M Y, h:i A'),
                'ip' => $activity->ip_address,
            ];
        });

        return response()->json($activities);
    }
}
