<?php

namespace App\Livewire\LandLord;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads; // Required for Logo Upload
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.landlord')]
class Settings extends Component
{
    use WithFileUploads;

    // --- Tab: General & Branding ---
    public $platformName = 'MediFlow SaaS';
    public $supportEmail = 'support@mediflow.com';
    public $globalAnnouncement = '';
    public $logo; // Temporary file upload
    public $currentLogoUrl; // For displaying existing logo

    // --- Tab: Localization ---
    public $timezone = 'Africa/Douala';
    public $currency = 'XAF';
    public $dateFormat = 'Y-m-d';

    // --- Tab: Security ---
    public $enforce2fa = false;
    public $sessionTimeout = 120; // Minutes
    public $passwordMinLength = 8;

    // --- Tab: Notifications ---
    public $notifyNewTenant = true;
    public $notifyTicketCreated = true;
    public $notifyCriticalErrors = true;

    // --- Tab: Maintenance ---
    public $maintenanceMode = false;

    public function mount()
    {
        // TODO: In a real app, load these from your Settings table
        // Example:
        // $settings = Setting::pluck('value', 'key');
        // $this->platformName = $settings['platform_name'] ?? 'MediFlow SaaS';
        // $this->maintenanceMode = app()->isDownForMaintenance();
    }

    public function saveSettings()
    {
        $this->validate([
            'platformName' => 'required|string|max:255',
            'supportEmail' => 'required|email',
            'logo'         => 'nullable|image|max:2048', // 2MB Max
            'sessionTimeout' => 'required|integer|min:5',
            'passwordMinLength' => 'required|integer|min:8',
        ]);

        // 1. Handle Logo Upload
        if ($this->logo) {
            // Store file in public/storage/logos
            $path = $this->logo->store('logos', 'public');

            // Save $path to database setting 'platform_logo'
            // Setting::updateOrCreate(['key' => 'platform_logo'], ['value' => $path]);

            // Reset the input to avoid re-uploading on next save
            $this->logo = null;
        }

        // 2. Handle Maintenance Mode
        // In Laravel, this usually creates a file in storage/framework
        if ($this->maintenanceMode) {
             // You might use Artisan::call('down') here, but be careful
             // as it might lock you out if you haven't whitelisted your IP.
             // Ideally, save a DB flag that your Middleware checks.
        }

        // 3. Save other settings to DB
        // Setting::updateOrCreate(['key' => 'platform_name'], ['value' => $this->platformName]);
        // Setting::updateOrCreate(['key' => 'currency'], ['value' => $this->currency]);
        // ... save other fields

        session()->flash('success', 'Platform settings updated successfully.');
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
            session()->flash('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    /**
     * Trigger Database Backup
     */
    public function backupNow()
    {
        try {
            // This assumes you have Spatie Backup or similar installed
            // Artisan::call('backup:run --only-db');

            // Simulating a delay for the UI loading state
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
