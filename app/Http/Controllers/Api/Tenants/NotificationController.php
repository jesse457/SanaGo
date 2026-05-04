<?php

namespace App\Http\Controllers\Api\Tenants;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications for the authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $this->logDebug('Fetching notifications', [
                'per_page' => $perPage,
            ]);

            $notifications = Notification::where('notifiable_id', Auth::id())
                ->where('notifiable_type', 'App\Models\User')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $this->logInfo('Notifications fetched successfully', [
                'count' => $notifications->total(),
                'per_page' => $perPage,
                'current_page' => $notifications->currentPage(),
            ]);

            return NotificationResource::collection($notifications);
        } catch (\Exception $e) {
            $this->logException($e);
            throw $e;
        }
    }

    /**
     * Display the specified notification.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $this->logDebug('Fetching notification', [
                'notification_id' => $id,
            ]);

            $notification = Notification::where('id', $id)
                ->where('notifiable_id', Auth::id())
                ->where('notifiable_type', 'App\Models\User')
                ->firstOrFail();

            // Mark as read when viewed
            if (!$notification->read_at) {
                $notification->update([
                    'read_at' => now()
                ]);
                $this->logInfo('Notification marked as read', [
                    'notification_id' => $id,
                ]);
            }

            $this->logInfo('Notification fetched successfully', [
                'notification_id' => $id,
                'type' => $notification->type,
            ]);

            return new NotificationResource($notification);
        } catch (\Exception $e) {
            $this->logException($e, [
                'notification_id' => $id,
            ]);
            throw $e;
        }
    }

    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead()
    {
        try {
            $count = Notification::where('notifiable_id', Auth::id())
                ->where('notifiable_type', 'App\Models\User')
                ->whereNull('read_at')
                ->count();

            Notification::where('notifiable_id', Auth::id())
                ->where('notifiable_type', 'App\Models\User')
                ->whereNull('read_at')
                ->update([
                    'read_at' => now()
                ]);

            $this->logInfo('All notifications marked as read', [
                'count' => $count,
            ]);

            return response()->json([
                'message' => 'All notifications marked as read',
                'data' => []
            ]);
        } catch (\Exception $e) {
            $this->logException($e);
            throw $e;
        }
    }

    /**
     * Mark a specific notification as read.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        try {
            $notification = Notification::where('id', $id)
                ->where('notifiable_id', Auth::id())
                ->where('notifiable_type', 'App\Models\User')
                ->firstOrFail();

            $notification->update([
                'read_at' => now()
            ]);

            $this->logInfo('Notification marked as read', [
                'notification_id' => $id,
            ]);

            return new NotificationResource($notification);
        } catch (\Exception $e) {
            $this->logException($e, [
                'notification_id' => $id,
            ]);
            throw $e;
        }
    }

    /**
     * Delete a specific notification.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $notification = Notification::where('id', $id)
                ->where('notifiable_id', Auth::id())
                ->where('notifiable_type', 'App\Models\User')
                ->firstOrFail();

            $notification->delete();

            $this->logInfo('Notification deleted successfully', [
                'notification_id' => $id,
            ]);

            return response()->json([
                'message' => 'Notification deleted successfully',
                'data' => []
            ]);
        } catch (\Exception $e) {
            $this->logException($e, [
                'notification_id' => $id,
            ]);
            throw $e;
        }
    }

    /**
     * Get unread notifications count.
     *
     * @return \Illuminate\Http\Response
     */
    public function unreadCount()
    {
        try {
            $count = Notification::where('notifiable_id', Auth::id())
                ->where('notifiable_type', 'App\Models\User')
                ->whereNull('read_at')
                ->count();

            $this->logDebug('Unread notifications count retrieved', [
                'count' => $count,
            ]);

            return response()->json([
                'count' => $count
            ]);
        } catch (\Exception $e) {
            $this->logException($e);
            throw $e;
        }
    }

    /**
     * Get unread notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function unread()
    {
        try {
            $perPage = request()->input('per_page', 15);
            $this->logDebug('Fetching unread notifications', [
                'per_page' => $perPage,
            ]);

            $notifications = Notification::where('notifiable_id', Auth::id())
                ->where('notifiable_type', 'App\Models\User')
                ->whereNull('read_at')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $this->logInfo('Unread notifications fetched successfully', [
                'count' => $notifications->total(),
                'per_page' => $perPage,
            ]);

            return NotificationResource::collection($notifications);
        } catch (\Exception $e) {
            $this->logException($e);
            throw $e;
        }
    }
}
