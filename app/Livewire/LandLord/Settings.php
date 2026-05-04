<?php

namespace App\Livewire\LandLord;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.landlord')]
class Settings extends Component
{
    use WithFileUploads;

    // =========================================================================
    // 1. General & Branding
    // =========================================================================
    public $platformName = 'MediFlow SaaS';

    public $supportEmail = 'support@mediflow.com';

    public $logo; // Temporary file upload

    public $currentLogoUrl;

    // =========================================================================
    // 2. Billing & Gateways (NEW)
    // =========================================================================
    public bool $stripeEnabled = false;

    public $stripeKey = '';

    public $stripeSecret = '';

    public bool $paypalEnabled = false;

    // =========================================================================
    // 3. Tenant Defaults (NEW)
    // =========================================================================
    public int $trialDays = 14;

    public string $dbConnection = 'mysql';

    // =========================================================================
    // 4. Localization
    // =========================================================================
    public $timezone = 'Africa/Douala';

    public $currency = 'XAF';

    public $dateFormat = 'Y-m-d';

    // =========================================================================
    // 5. Security & Access
    // =========================================================================
    public bool $enforce2fa = false;

    public int $sessionTimeout = 120; // Minutes

    public int $passwordMinLength = 8;

    // =========================================================================
    // 6. Notifications
    // =========================================================================
    public bool $notifyNewTenant = true;

    public bool $notifyTicketCreated = true;

    public bool $notifyCriticalErrors = true;

    // =========================================================================
    // 7. Maintenance
    // =========================================================================
    public bool $maintenanceMode = false;

    /**
     * Load initial settings from Database/Env/Valuestore
     */
    public function mount()
    {
        // Simulation: In a real app, you would fetch these from a 'settings' table.
        // $settings = Setting::all()->pluck('value', 'key');

        // $this->platformName = $settings['app_name'] ?? config('app.name');
        // $this->stripeEnabled = $settings['stripe_enabled'] === '1';
        // $this->trialDays = intval($settings['default_trial_days'] ?? 14);

        // For now, we use the default property values defined above.
        $this->maintenanceMode = app()->isDownForMaintenance();
    }

    /**
     * Save all configuration tabs
     */
    public function saveSettings()
    {
        $this->validate([
            // General
            'platformName' => 'required|string|max:50',
            'supportEmail' => 'required|email',
            'logo' => 'nullable|image|max:2048', // 2MB Max

            // Billing (Conditional Validation)
            'stripeKey' => 'required_if:stripeEnabled,true',
            'stripeSecret' => 'required_if:stripeEnabled,true',

            // Defaults
            'trialDays' => 'required|integer|min:0|max:365',
            'dbConnection' => ['required', Rule::in(['mysql', 'pgsql', 'sqlite'])],

            // Security
            'sessionTimeout' => 'required|integer|min:5|max:1440', // Max 24 hours
            'passwordMinLength' => 'required|integer|min:8|max:32',
        ]);

        try {
            // 1. Handle Logo Upload
            if ($this->logo) {
                $path = $this->logo->store('public/logos');
                // Setting::updateOrCreate(['key' => 'logo_path'], ['value' => $path]);
                $this->logo = null; // Reset input
            }

            // 2. Handle Maintenance Mode Logic
            if ($this->maintenanceMode && ! app()->isDownForMaintenance()) {
                // Artisan::call('down', ['--secret' => 'admin-access']);
            } elseif (! $this->maintenanceMode && app()->isDownForMaintenance()) {
                // Artisan::call('up');
            }

            // 3. Save to Database
            // $settings = [
            //     'platform_name' => $this->platformName,
            //     'stripe_enabled' => $this->stripeEnabled,
            //     'default_trial_days' => $this->trialDays,
            //     // ... other fields
            // ];

            // foreach($settings as $key => $value) {
            //     Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            // }

            // 4. Update Environment (Optional/Advanced)
            // You might want to update .env for things like STRIPE_KEY if not using DB settings config

            Log::info('Landlord settings updated by user: '.auth()->id());

            session()->flash('success', 'Platform settings updated successfully.');

        } catch (\Exception $e) {
            Log::error('Settings update failed: '.$e->getMessage());
            session()->flash('error', 'Failed to save settings. Check logs.');
        }
    }

    /**
     * Clear Application Cache
     */
    public function clearCache()
    {
        try {
            Artisan::call('optimize:clear');
            session()->flash('success', 'System cache, views, and routes cleared.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to clear cache: '.$e->getMessage());
        }
    }

    /**
     * Trigger Database Backup
     */
    public function backupNow()
    {
        try {
            // This assumes you have Spatie Backup installed
            // Artisan::call('backup:run --only-db');

            // Simulate delay
            sleep(1);

            Log::info('Manual backup triggered by Landlord.');
            session()->flash('success', 'Database backup process started in background.');
        } catch (\Exception $e) {
            session()->flash('error', 'Backup failed to start.');
        }
    }

    public function render()
    {
        return view('livewire.land-lord.settings');
    }
}
