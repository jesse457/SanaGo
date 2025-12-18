<?php

namespace App\Notifications;

use App\Models\Prescription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Broadcasting\PrivateChannel;

class NewPrescriptionOrder extends Notification implements ShouldQueue
{
    use Queueable;

    public $prescription;

    public function __construct(Prescription $prescription)
    {
        $this->prescription = $prescription;
    }

    public function via(object $notifiable): array
    {
        // Store in DB for history, Broadcast for real-time
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
            'message' => 'New Prescription Order',
            'patient_name' => $this->prescription->patient->first_name . ' ' . $this->prescription->patient->last_name,
            'prescription_id' => $this->prescription->id,
            'type' => 'pharmacy_order',
            'created_at' => now()->toIso8601String(),
        ];
    }

    /**
     * IMPORTANT: This tells Laravel to broadcast to the DEPARTMENT channel
     * instead of the individual User channel.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('pharmacy.orders'),
        ];
    }
}
