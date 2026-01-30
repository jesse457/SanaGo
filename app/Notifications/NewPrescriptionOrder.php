<?php

namespace App\Notifications;

use App\Models\Prescription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage; // Important
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // Important

class NewPrescriptionOrder extends Notification implements ShouldBroadcast
{
    use Queueable;

    public $prescription;

    public function __construct(Prescription $prescription)
    {
        $this->prescription = $prescription;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        // 'database' for the bell icon history
        // 'broadcast' for the real-time toast/popup
        return ['database', 'broadcast'];
    }

    /**
     * 1. Data stored in the Database (json column)
     */
    public function toArray($notifiable)
    {
        return [
            'id' => $this->prescription->id,
            'message' => 'New prescription received for ' . $this->prescription->patient->name,
            'patient_id' => $this->prescription->patient_id,
            'doctor_name' => $this->prescription->doctor->name ?? 'Unknown Doctor',
            'timestamp' => now()->toIso8601String(),
            'type' => 'prescription'
        ];
    }

    /**
     * 2. Data sent over WebSocket (Reverb)
     */
    public function toBroadcast($notifiable): BroadcastMessage
    {
        // This is the actual data you see in the browser console
        return new BroadcastMessage([
            'id' => $this->prescription->id,
            'title' => 'New Prescription',
            'message' => 'New prescription received for ' . $this->prescription->patient->name,
            'patient_name' => $this->prescription->patient->name,
            'link' => '/pharmacy/dispense/' . $this->prescription->id,
            'type' => 'prescription', // Helps frontend decide icon/color
        ]);
    }

    /**
     * OPTIONAL: If you want to use 'pharmacy.orders' channel instead of the specific user channel.
     * WARNING: Only do this if you are broadcasting ONE event.
     * Since your Service loops through users, keep this commented out or default behavior
     * will correctly target App.Models.User.{id}
     */
    // public function broadcastOn()
    // {
    //     return new PrivateChannel('pharmacy.orders');
    // }
}
