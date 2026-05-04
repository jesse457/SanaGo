<?php

namespace App\Notifications;

use App\Models\Admission;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewPatientAdmissionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $admission;

    public function __construct(Admission $admission)
    {
        $this->admission = $admission->load('patient', 'doctor');
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
            'message' => 'New Patient Admission',
            'patient_name' => $this->admission->patient->first_name.' '.$this->admission->patient->last_name,
            'doctor_name' => $this->admission->doctor->name ?? 'Unknown Doctor',
            'admission_id' => $this->admission->id,
            'ward_name' => $this->admission->ward->name ?? 'Unknown Ward',
            'bed_number' => $this->admission->bed->bed_number ?? 'Unknown Bed',
            'type' => 'patient_admission',
            'created_at' => now()->toIso8601String(),
        ];
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('nurse.admissions'),
        ];
    }
}
