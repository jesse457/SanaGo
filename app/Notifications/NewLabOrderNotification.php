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
     * Logic: If tech is online, just Broadcast. If offline, store in DB.
     */
    public function via($notifiable)
    {
        $isOnline = Cache::has('user-online-' . $notifiable->id);

        if ($isOnline) {
            return ['broadcast'];
        }

        return ['database', 'broadcast'];
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.' . $this->labRequest->lab_tech_id),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->getData());
    }

    public function toArray($notifiable)
    {
        return $this->getData();
    }

    private function getData()
    {
        return [
            'id' => $this->id,
            'message' => 'New Lab Order: ' . ($this->labRequest->testDefinition->test_name ?? 'Test'),
            'patient_name' => $this->labRequest->patient->first_name . ' ' . $this->labRequest->patient->last_name,
            'urgency' => $this->labRequest->urgency_level,
            'doctor_name' => $this->labRequest->doctor->name ?? 'Unknown Doctor',
            'request_id' => $this->labRequest->id,
            'created_at' => now()->toIso8601String(),
            'type' => 'new_order' // To distinguish icon/color in frontend
        ];
    }
}
