<?php

namespace App\Notifications;

use App\Models\MedicalRecord; // Or LabRequest, depending on how you pass data
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Broadcasting\PrivateChannel;

class NewLabOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $medicalRecord;

    // We pass the MedicalRecord (Consultation) to group multiple tests
    public function __construct(MedicalRecord $medicalRecord)
    {
        $this->medicalRecord = $medicalRecord;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->getData());
    }

    public function toArray(object $notifiable): array
    {
        return $this->getData();
    }

    private function getData(): array
    {
        return [
            'id' => $this->id,
            'message' => 'New Lab Request(s) Available',
            'patient_name' => $this->medicalRecord->patient->first_name . ' ' . $this->medicalRecord->patient->last_name,
            'consultation_id' => $this->medicalRecord->id,
            'type' => 'lab_order',
            'created_at' => now()->toIso8601String(),
        ];
    }

    /**
     * IMPORTANT: Broadcast to the Lab Department Channel
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('lab.requests'),
        ];
    }
}
