<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class AppointmentReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment->load('patient', 'doctor');
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'data' => $this->getData(),
            'read_at' => null,
            'created_at' => now()->toIso8601String(),
        ]);
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->getData();
    }

    public function toArray(object $notifiable): array
    {
        return $this->getData();
    }

    private function getData(): array
    {
        return [
            'id' => $this->id,
            'message' => 'Appointment Reminder',
            'patient_name' => $this->appointment->patient->first_name.' '.$this->appointment->patient->last_name,
            'doctor_name' => $this->appointment->doctor->name ?? 'Unknown Doctor',
            'appointment_id' => $this->appointment->id,
            'appointment_time' => $this->appointment->appointment_time->toIso8601String(),
            'type' => 'appointment_reminder',
            'created_at' => now()->toIso8601String(),
        ];
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.'.$this->appointment->doctor_id),
        ];
    }
}
