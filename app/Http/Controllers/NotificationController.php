<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            ->when(tenant() !== null && ! $user->can('view-finance'), function ($q) {
                // Inside a hospital, non-finance users only see clinical
                // notifications. The landlord console has no tenant
                // permissions, so its platform/billing feed is never filtered.
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
    public function markRead(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $notification = auth()->user()->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        // Inertia visits need a redirect so the page props refresh; the bell
        // dropdown and tests call this endpoint as JSON instead.
        if ($request->expectsJson() && ! $request->header('X-Inertia')) {
            return response()->json(['ok' => true]);
        }

        return back();
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
            ->when(tenant() !== null && ! $user->can('view-finance'), function ($q) {
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
