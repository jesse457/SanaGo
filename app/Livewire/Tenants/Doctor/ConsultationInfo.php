<?php

namespace App\Livewire\Tenants\Doctor;

use Livewire\Attributes\Layout;
use Livewire\Component;

// Use the doctor layout for this Livewire component
#[Layout('components.layouts.doctor')]
class ConsultationInfo extends Component
{
    public function render()
    {
        return view('livewire.tenants.doctor.consultation-info');
    }
}
