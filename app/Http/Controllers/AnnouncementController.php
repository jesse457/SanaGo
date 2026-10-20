<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class AnnouncementController extends Controller
{
    public function index(): Response
    {
        $announcements = Announcement::latest()->get()->map(fn (Announcement $a) => [
            'id' => $a->id,
            'title' => $a->title,
            'body' => $a->body,
            'type' => $a->type,
            'is_published' => $a->is_published,
            'published_at' => $a->published_at?->toDateTimeString(),
            'expires_at' => $a->expires_at?->toDateTimeString(),
            'created_at' => $a->created_at?->toDateTimeString(),
        ]);

        return Inertia::render('Announcements/Index', ['announcements' => $announcements]);
    }

    public function create(): Response
    {
        return Inertia::render('Announcements/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'type' => ['required', 'string', 'in:info,warning,success'],
            'is_published' => ['boolean'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $validated['published_at'] = $validated['is_published'] ? now() : null;

        Announcement::create($validated);

        return Redirect::route('announcements.index')->with('success', 'Announcement created.');
    }

    public function edit(Announcement $announcement): Response
    {
        return Inertia::render('Announcements/Edit', [
            'announcement' => [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'body' => $announcement->body,
                'type' => $announcement->type,
                'is_published' => $announcement->is_published,
                'expires_at' => $announcement->expires_at?->toDateString(),
            ],
        ]);
    }

    /**
     * Edit an announcement, or toggle its publication state.
     *
     * Previously an announcement could only be created or deleted, so a
     * published notice could never be corrected or withdrawn.
     */
    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'body' => ['sometimes', 'required', 'string', 'max:5000'],
            'type' => ['sometimes', 'required', 'string', 'in:info,warning,success'],
            'is_published' => ['sometimes', 'boolean'],
            'expires_at' => ['nullable', 'date'],
        ]);

        if (array_key_exists('is_published', $validated)) {
            $validated['published_at'] = $validated['is_published']
                ? ($announcement->published_at ?? now())
                : null;
        }

        $announcement->update($validated);

        return Redirect::route('announcements.index')->with('success', 'Announcement updated.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        return Redirect::route('announcements.index')->with('success', 'Announcement deleted.');
    }
}
