<?php

namespace App\Livewire\LandLord;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.landlord')]
class Settings extends Component
{
    // Platform Identity
    public $platformName = 'MediFlow SaaS';

    public $supportEmail = 'support@mediflow.com';

    // Localization
    public $timezone = 'Africa/Douala';

    public $currency = 'XAF';

    // Notifications (SaaS Admin preferences)
    public $notifyNewTenant = true;

    public $notifyTicketCreated = true;

    public $marketingEmails = false;

    public function mount()
    {
        // Ideally, load these from a 'Settings' model or config file
        // $settings = Setting::all()->pluck('value', 'key');
        // $this->platformName = $settings['platform_name']; etc.
    }

    public function saveSettings()
    {
        // Validation
        $this->validate([
            'platformName' => 'required|string|max:255',
            'supportEmail' => 'required|email',
        ]);

        // Logic to save to DB (e.g., Settings::updateOrCreate(...))

        session()->flash('success', 'Platform settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.land-lord.settings');
    }
}
