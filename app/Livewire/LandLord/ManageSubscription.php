<?php

namespace App\Livewire\Landlord;

use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User; // Assuming User model exists in Tenant context
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

    public int $currentStorage = 0; // In MB

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

            // Calculate Real Usage
            // Note: Adjust 'User' class import if your tenant user model is namespaced differently
            try {
                $this->currentUsers = User::count();
            } catch (\Exception $e) {
                $this->currentUsers = 0;
            }

            // Mocking storage calculation (or replace with your actual file storage logic)
            $this->currentStorage = 450;
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
        // Ensure you have the 'universal' middleware group set up or use the helper
        return redirect()->route('impersonate', $this->tenant->id);
    }

    public function render()
    {
        return view('livewire.land-lord.manage-subscription');
    }
}
