<?php

namespace App\Notifications;

use App\Models\LabRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\Cache;

class NewLabOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $labRequest;

    public function __construct(LabRequest $labRequest)
    {
        $this->labRequest = $labRequest;
    }

    /**
     * Logic: Defines which channels to send to.
     */
    public function via(object $notifiable): array
    {
        // Check if the specific user is online
        $isOnline = Cache::has('user-online-' . $notifiable->id);

        // If online, send a Toast (Broadcast).
        // If offline, save to History (Database) AND Broadcast (in case they just reconnected).
        // Note: Usually you want 'database' always for history, but sticking to your logic:
        return $isOnline ? ['broadcast'] : ['database', 'broadcast'];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->getData());
    }

    /**
     * Get the array representation of the notification (for Database).
     */
    public function toArray(object $notifiable): array
    {
        return $this->getData();
    }

    /**
     * Helper to centralize data structure.
     */
    private function getData(): array
    {
        return [
            'id' => $this->id, // Notification ID
            'message' => 'New Lab Order: ' . ($this->labRequest->testDefinition->test_name ?? 'Test'),
            'patient_name' => $this->labRequest->patient->first_name . ' ' . $this->labRequest->patient->last_name,
            'urgency' => $this->labRequest->urgency_level,
            'request_id' => $this->labRequest->id,
            'type' => 'new_order',
            'created_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Optional: Explicitly define the channel if not using the default `App.Models.User.{id}`
     */
    public function broadcastOn(): array
    {
        // This targets the specific Lab Tech
        return [
            new PrivateChannel('App.Models.User.' . $this->labRequest->lab_tech_id),
        ];
    }
}
