<?php

namespace App\Notifications;

use App\Models\MedicalRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewLabOrderNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public $medicalRecord;

    /**
     * Create a new notification instance.
     */
    public function __construct(MedicalRecord $medicalRecord)
    {
        $this->medicalRecord = $medicalRecord;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification (Database).
     */
    public function toArray($notifiable)
    {
        return [
            'id' => $this->medicalRecord->id,
            'title' => 'New Lab Request',
            'message' => 'New Lab Request for ' . $this->medicalRecord->patient->name,
            'patient_id' => $this->medicalRecord->patient_id,
            'doctor_name' => $this->medicalRecord->doctor->name ?? 'Unknown Doctor',
            'link' => '/laboratory/requests', // Frontend route
            'type' => 'lab_request',
            'urgency' => 'Normal',
        ];
    }

    /**
     * Get the broadcast representation of the notification (WebSocket).
     */
    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->medicalRecord->id,
            'title' => 'New Lab Request',
            'message' => 'New Lab Request for ' . $this->medicalRecord->patient->name,
            'patient_name' => $this->medicalRecord->patient->name,
            'link' => '/laboratory/requests', // Frontend route
            'type' => 'lab_request',
            'created_at' => now()->toIso8601String(),
        ]);
    }
}
