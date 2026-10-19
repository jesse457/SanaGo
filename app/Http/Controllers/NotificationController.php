<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Notifications\DatabaseNotification;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    /**
     * List all notifications for the authenticated user,
     * filtered by what the user's role can see.
     */
    public function index(): Response
    {
        $user = auth()->user();

        $notifications = $user->notifications()
            ->when(! $user->can('view-finance'), function ($q) {
                // Non-admin users only see clinical notifications
                $q->whereIn('data->category', ['clinical', 'lab', 'pharmacy', 'radiology', 'general']);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications->through(fn (DatabaseNotification $n) => [
                'id' => $n->id,
                'type' => $n->data['type_label'] ?? 'info',
                'message' => $n->data['message'] ?? '',
                'category' => $n->data['category'] ?? 'general',
                'link' => $n->data['link'] ?? null,
                'read' => $n->read_at !== null,
                'created_at' => $n->created_at->diffForHumans(),
            ]),
            'unreadCount' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(string $id): JsonResponse
    {
        $notification = auth()->user()->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        return response()->json(['ok' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead(): RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Fetch unread count + latest notifications (for the bell dropdown).
     */
    public function latest(): JsonResponse
    {
        $user = auth()->user();

        $notifications = $user->unreadNotifications()
            ->when(! $user->can('view-finance'), function ($q) {
                $q->whereIn('data->category', ['clinical', 'lab', 'pharmacy', 'radiology', 'general']);
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(fn (DatabaseNotification $n) => [
                'id' => $n->id,
                'type' => $n->data['type_label'] ?? 'info',
                'message' => $n->data['message'] ?? '',
                'category' => $n->data['category'] ?? 'general',
                'link' => $n->data['link'] ?? null,
                'created_at' => $n->created_at->diffForHumans(),
            ]);

        return response()->json([
            'unreadCount' => $user->unreadNotifications()->count(),
            'notifications' => $notifications,
        ]);
    }
}
