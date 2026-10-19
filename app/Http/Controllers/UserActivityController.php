<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Permission;

class UserActivityController extends Controller
{
    public function index(Request $request)
    {
        // dd(Permission::all());

        $activities = Activity::with('causer')
            ->latest()
            // Search Filter (Supports searching by description, model type, or causer name/email)
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                        ->orWhere('subject_type', 'like', "%{$search}%")
                        ->orWhereHasMorph('causer', [User::class], function ($morphQuery) use ($search) {
                            $morphQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
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
                'activity_type' => $activity->description, // Spatie usually stores event types ('created', 'updated') here
                'description' => $this->formatDescription($activity),
                'subject_type' => $activity->subject_type ? class_basename($activity->subject_type) : null,
                'subject_id' => $activity->subject_id,
                'created_at' => $activity->created_at,
                'user' => $activity->causer ? [
                    'name' => $activity->causer->name,
                    'email' => $activity->causer->email,
                ] : ['name' => 'System', 'email' => 'N/A'],
                'metadata' => $activity->properties,
                // Explicitly mapping Spatie's newer attribute_changes column with old/new fallback
                'attribute_changes' => $activity->attribute_changes ?? [
                    'attributes' => $activity->properties['attributes'] ?? null,
                    'old' => $activity->properties['old'] ?? null,
                ],
                'ip_address' => $activity->properties['ip'] ?? 'N/A',
                'user_agent' => $activity->properties['user_agent'] ?? 'N/A',
            ]);

        return Inertia::render('Settings/Activities', [
            'activities' => $activities,
            'filters' => $request->only(['search', 'type', 'date']),
        ]);
    }

    /**
     * Format a readable activity description.
     */
    protected function formatDescription($activity)
    {
        $subjectName = $activity->subject_type
            ? strtolower(class_basename($activity->subject_type))
            : 'record';

        return "{$activity->description} a {$subjectName}";
    }
}
