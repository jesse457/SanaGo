<?php

namespace App\Events;

use App\Models\MedicalRecord;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewLabRequestEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $medicalRecord;

    public function __construct(MedicalRecord $medicalRecord)
    {
        $this->medicalRecord = $medicalRecord->load('patient');
    }

    /**
     * Broadcast to the 'lab.requests' channel.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('lab.requests'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new.request';
    }

    public function broadcastWith(): array
    {
        return [
            'consultation_id' => $this->medicalRecord->id,
            'patient_name' => $this->medicalRecord->patient->first_name . ' ' . $this->medicalRecord->patient->last_name,
            'doctor_name' => $this->medicalRecord->doctor->name ?? 'Unknown',
            'urgency' => 'normal', // You could calculate max urgency here if needed
            'created_at' => now()->toIso8601String(),
        ];
    }
}
