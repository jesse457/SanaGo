<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\DemoRequest;
use App\Models\Note;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class DemoRequestController extends Controller
{
    public function index(): Response
    {
        $demoRequests = DemoRequest::with('assignedTo')
            ->latest()
            ->get()
            ->map(fn (DemoRequest $d) => [
                'id' => $d->id,
                'name' => $d->name,
                'email' => $d->email,
                'company' => $d->company,
                'message' => $d->message,
                'status' => $d->status,
                'pipeline_status' => $d->pipeline_status,
                'assigned_to' => $d->assignedTo?->name,
                'follow_up_date' => $d->follow_up_date,
                'created_at' => $d->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('DemoRequests/Index', ['demoRequests' => $demoRequests]);
    }

    public function show(DemoRequest $demoRequest): Response
    {
        $demoRequest->load(['assignedTo', 'notes.user']);

        return Inertia::render('DemoRequests/Show', [
            'demoRequest' => [
                'id' => $demoRequest->id,
                'name' => $demoRequest->name,
                'email' => $demoRequest->email,
                'company' => $demoRequest->company,
                'message' => $demoRequest->message,
                'status' => $demoRequest->status,
                'pipeline_status' => $demoRequest->pipeline_status,
                'assigned_to' => $demoRequest->assignedTo?->only(['id', 'name']),
                'follow_up_date' => $demoRequest->follow_up_date,
                'created_at' => $demoRequest->created_at?->toDateTimeString(),
                'notes' => $demoRequest->notes->map(fn (Note $n) => [
                    'id' => $n->id,
                    'note' => $n->note,
                    'user_name' => $n->user?->name,
                    'created_at' => $n->created_at?->toDateTimeString(),
                ]),
            ],
        ]);
    }

    public function updateStatus(Request $request, DemoRequest $demoRequest): RedirectResponse
    {
        $validated = $request->validate([
            'pipeline_status' => ['required', 'string', 'in:new,contacted,demo_scheduled,negotiating,converted,rejected'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'follow_up_date' => ['nullable', 'date'],
        ]);

        $demoRequest->update($validated);

        return Redirect::back()->with('flash', 'Pipeline status updated.');
    }

    public function addNote(Request $request, DemoRequest $demoRequest): RedirectResponse
    {
        $validated = $request->validate([
            'note' => ['required', 'string', 'max:2000'],
        ]);

        $demoRequest->notes()->create([
            'user_id' => $request->user()?->id,
            'note' => $validated['note'],
        ]);

        return Redirect::back()->with('flash', 'Note added.');
    }
}
