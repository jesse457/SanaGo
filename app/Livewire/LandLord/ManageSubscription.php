<?php

namespace App\Livewire\LandLord;

use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.landlord')]
class ManageSubscription extends Component
{
    public Tenant $tenant;

    public ?Subscription $subscription;

    // Usage Stats
    public int $currentUsers = 0;
    public int $currentStorage = 0; // In MB (integer for progress bars)
    public string $formattedStorage = '0 B'; // Human readable string
    public float $storagePercentage = 0;

    public function mount(Tenant $tenant)
    {
        $this->tenant = $tenant->load('domains');
        $this->loadTenantData();
    }

    public function loadTenantData()
    {
        // Execute inside the Tenant's Database Connection
        $this->tenant->run(function () {
            $this->subscription = Subscription::first();

            // 1. Calculate Real Users (Live count)
            try {
                // Ensure App\Models\User is the correct path for the Tenant User model
                $this->currentUsers = User::count();
            } catch (\Exception $e) {
                $this->currentUsers = 0;
            }

            // 2. Get Storage from Metadata (Cached by Scheduler)
            // We read from the 'usage_stats' key we populated in the Console Command
            if ($this->subscription && !empty($this->subscription->metadata['usage_stats'])) {
                $stats = $this->subscription->metadata['usage_stats'];

                // Get raw bytes
                $bytes = $stats['bytes'] ?? 0;

                // Convert bytes to MB for the integer property (useful for logic/progress bars)
                $this->currentStorage = round($bytes / 1024 / 1024);

                // Get the pre-formatted string (e.g., "1.2 GB")
                $this->formattedStorage = $stats['formatted'] ?? '0 B';

                // Get the percentage
                $this->storagePercentage = floatval($stats['percentage'] ?? 0);
            } else {
                // Fallback if the scheduler hasn't run yet
                $this->currentStorage = 0;
                $this->formattedStorage = '0 B';
                $this->storagePercentage = 0;
            }
        });
    }

    public function cancelSubscription()
    {
        $this->tenant->run(function () {
            $sub = Subscription::first();
            if ($sub) {
                $sub->cancel();
            }
        });

        $this->loadTenantData();
        LivewireAlert::title('Subscription Cancelled')->warning()->show();
    }

    public function resumeSubscription()
    {
        $this->tenant->run(function () {
            $sub = Subscription::first();
            if ($sub) {
                $sub->resume();
            }
        });

        $this->loadTenantData();
        LivewireAlert::title('Subscription Resumed')->success()->show();
    }

    public function loginAsTenant()
    {
        // Stancl Tenancy Impersonation Logic
        return redirect()->route('impersonate', $this->tenant->id);
    }

    public function render()
    {
        return view('livewire.land-lord.manage-subscription');
    }
}
