<?php

namespace App\Events;

use App\Models\LabResult;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class LabResultCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $labResult;

    /**
     * Create a new event instance.
     */
    public function __construct(LabResult $labResult)
    {
        $this->labResult = $labResult;
    }

    /**
     * Logic: If tech is online, just Broadcast. If offline, store in DB.
     */
    public function via($notifiable)
    {
        $isOnline = Cache::has('user-online-'.$notifiable->id);

        if ($isOnline) {
            return ['broadcast'];
        }

        return ['database', 'broadcast'];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.'.$this->labResult->doctor_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'message' => 'Lab result completed for '.($this->labResult->labRequest->patient->name ?? 'Patient'),
            'lab_result_id' => $this->labResult->id,
            // Use ?-> to safely access nested properties
            'patient_name' => $this->labResult->labRequest?->patient?->name,
            'test_name' => $this->labResult->labRequest?->testDefinition?->test_name,
            'status' => $this->labResult->status,
        ];
    }
}
