<?php

namespace App\Http\Controllers;

use App\Models\LabRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LabRequestController extends Controller
{
    public function index(Request $request)
    {
        // 1. Build Query with Eager Loading
        $query = LabRequest::with(['patient', 'testDefinition', 'doctor'])
            ->latest();

        // 2. Server-side Filtering
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($p) use ($search) {
                    $p->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('patient_uid', 'like', "%{$search}%");
                })->orWhereHas('testDefinition', function ($t) use ($search) {
                    $t->where('test_name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 3. Calculate Stats (Efficiently)
        $statsData = LabRequest::selectRaw("
            count(*) as total,
            count(case when status = 'Completed' then 1 end) as completed,
            count(case when status = 'In_Progress' then 1 end) as in_progress,
            count(case when status = 'Pending' then 1 end) as pending
        ")->first();

        return Inertia::render('LabTests/Requests/Index', [
            'requests' => $query->paginate(12)->withQueryString(),
            'filters' => $request->only(['search', 'status']),
            'stats' => [
                'total' => $statsData->total,
                'completed' => $statsData->completed,
                'inProgress' => $statsData->in_progress,
                'pending' => $statsData->pending,
            ],
        ]);
    }
}
