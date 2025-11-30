<?php

namespace App\Livewire\Tenants\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class AiAssistant extends Component
{
    public function render()
    {
        return view('livewire.tenants.admin.ai-assistant');
    }
}
