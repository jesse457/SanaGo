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
        // 1. Always save to database so the history is preserved
        // 2. Broadcast to Reverb for real-time alert
        return ['database', 'broadcast'];
    }

    /**
     * DEFINE THE WEBSOCKET CHANNEL
     */
    public function broadcastOn(): array
    {
        // Ensure this matches the channel name in your Frontend Echo listener
        // Channel: private-App.Models.User.{id}
        return [
            new PrivateChannel('App.Models.User.' . $this->labResult->doctor_id),
        ];
    }

    /**
     * DATA FOR DATABASE (Offline Storage)
     */
    public function toDatabase($notifiable)
    {
        return $this->getData();
    }

    /**
     * DATA FOR REVERB (Online Broadcast)
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'data' => $this->getData(), // Wrap in 'data' to match standard structure
            'read_at' => null,
            'created_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Shared Data Structure
     */
    private function getData()
    {
        return [
            'id' => $this->id,
            'message' => 'Lab result completed for ' . ($this->labResult->labRequest->patient->first_name ?? 'Patient'),
            'lab_result_id' => $this->labResult->id,
            'patient_name' => ($this->labResult->labRequest->patient->first_name ?? '') . ' ' . ($this->labResult->labRequest->patient->last_name ?? ''),
            'test_name' => $this->labResult->labRequest->testDefinition->test_name ?? 'Unknown Test',
            'urgency' => $this->labResult->labRequest->urgency_level ?? 'normal',
            'status' => $this->labResult->status,
            'type' => 'lab_result'
        ];
    }
}
