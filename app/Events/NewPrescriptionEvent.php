<?php

namespace App\Events;

use App\Models\Prescription;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewPrescriptionEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $prescription;

    public function __construct(Prescription $prescription)
    {
        $this->prescription = $prescription;
    }

    /**
     * Reverb Channel: 'pharmacy.orders'
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('pharmacy.orders'),
        ];
    }

    /**
     * Event Name for Listener: '.new.order'
     */
    public function broadcastAs(): string
    {
        return 'new.order';
    }

    /**
     * Data payload sent to Reverb (and then to JS)
     */
    public function broadcastWith(): array
    {
        // Load relationships safely
        $this->prescription->loadMissing(['patient', 'doctor']);

        return [
            'id' => $this->prescription->id,
            'patient_name' => optional($this->prescription->patient)->first_name . ' ' . optional($this->prescription->patient)->last_name,
            'doctor_name' => optional($this->prescription->doctor)->name,
            'message' => 'New Prescription Order',
            'created_at' => $this->prescription->created_at->toIso8601String(),
        ];
    }
}
