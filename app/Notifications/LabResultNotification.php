<?php

namespace App\Notifications;

use App\Models\LabResult;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\Cache;

class LabResultNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $labResult;

    public function __construct(LabResult $labResult)
    {
        $this->labResult = $labResult;
    }

    /**
     * THE EFFICIENCY LOGIC
     */
    public function via($notifiable)
    {
        // Check the cache key set by the Heartbeat route
        $isOnline = Cache::has('user-online-' . $notifiable->id);

        if ($isOnline) {
            // User is online: Send via Reverb ONLY (No DB Write)
            return ['broadcast'];
        }

        // User is offline: Save to DB so they can fetch it later + Try broadcast
        return ['database', 'broadcast'];
    }

    /**
     * DEFINE THE WEBSOCKET CHANNEL
     */
    public function broadcastOn(): array
    {
        // Ensure this matches the channel name in your Frontend Echo listener
        // Assuming the doctor is the 'notifiable' entity
        return [
            new PrivateChannel('App.Models.User.' . $this->labResult->doctor_id),
        ];
    }

    /**
     * DATA FOR DATABASE (Offline Storage)
     */
    public function toArray($notifiable)
    {
        return $this->getData();
    }

    /**
     * DATA FOR REVERB (Online Broadcast)
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->getData());
    }

    /**
     * Shared Data Structure
     */
    private function getData()
    {
        return [
            'id' => $this->id, // Important for de-duplication
            'message' => 'Lab result completed for ' . ($this->labResult->labRequest->patient->name ?? 'Patient'),
            'lab_result_id' => $this->labResult->id,
            'patient_name' => $this->labResult->labRequest?->patient?->name,
            'test_name' => $this->labResult->labRequest?->testDefinition?->test_name,
            'status' => $this->labResult->status,
            'created_at' => now()->toIso8601String(),
            // If you need tenant_id logic here manually for the DB JSON:
            // 'tenant_id' => $this->labResult->tenant_id,
        ];
    }
}
