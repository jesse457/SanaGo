<?php

namespace App\Livewire\LandLord;

use Livewire\Attributes\Layout;
use Livewire\Component;

// Use the doctor layout for this Livewire component
#[Layout('components.layouts.landlord')]
class Settings extends Component
{
    public $landlordName;

    public $email;

    public $timezone = 'America/New_York';

    public $currency = 'USD';

    public $darkMode = false;

    public function mount()
    {
        // Load settings from the database or a configuration file.
        // For demonstration, we'll use placeholder values.
        $this->landlordName = 'Your Landlord Name';
        $this->email = 'your.email@example.com';
    }

    public function saveSettings()
    {
        // Add logic to validate and save settings to the database.
        // For now, we'll just show a success message.
        session()->flash('success', 'Settings saved successfully!');
    }

    public function render()
    {
        return view('livewire.land-lord.settings');
    }
}
