<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class UserActivityController extends Controller
{
    public function index(Request $request)
    {
        $activities = Activity::with('causer')
            ->latest()
            // Search Filter
            ->when($request->search, function ($query, $search) {
                $query->where('description', 'like', "%{$search}%")
                    ->orWhereHasMorph('causer', [User::class], function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            // Type/Description Filter (created, updated, deleted, etc)
            ->when($request->type, function ($query, $type) {
                $query->where('description', $type);
            })
            // Date Filter
            ->when($request->date, function ($query, $date) {
                $query->whereDate('created_at', $date);
            })
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($activity) => [
                'id' => $activity->id,
                'activity_type' => $activity->description, // Spatie usually stores 'created', 'updated' here
                'description' => $this->formatDescription($activity),
                'created_at' => $activity->created_at,
                'user' => $activity->causer ? [
                    'name' => $activity->causer->name,
                    'email' => $activity->causer->email,
                ] : ['name' => 'System', 'email' => 'N/A'],
                'metadata' => $activity->properties,
                'ip_address' => $activity->properties['ip'] ?? 'N/A',
                'user_agent' => $activity->properties['user_agent'] ?? 'N/A',
            ]);

        return Inertia::render('Settings/Activities', [
            'activities' => $activities,
            'filters' => $request->only(['search', 'type', 'date']),
        ]);
    }

    protected function formatDescription($activity)
    {
        $subjectName = $activity->subject_type
            ? strtolower(class_basename($activity->subject_type))
            : 'record';

        return "{$activity->description} a {$subjectName}";
    }
}
