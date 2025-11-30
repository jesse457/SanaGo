<?php

namespace App\Livewire\Tenants\Doctor;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.doctor')]
class MedicalExplainer extends Component
{
    public function render()
    {
        return view('livewire.tenants.doctor.medical-explainer');
    }
}
