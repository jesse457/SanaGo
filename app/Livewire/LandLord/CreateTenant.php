<?php

namespace App\Livewire\LandLord;

use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

#[Layout('components.layouts.landlord')]
class CreateTenant extends Component
{
    use WithFileUploads;

    public $tenantName;

    public $phoneNumber;

    public $address;

    public $logo = null;

    public $generatedDomain;

    public $hospitalContactEmail;

    // Subscription & Admin
    public $subscriptionTier = Subscription::PLAN_BASIC;

    public $billingCycle = Subscription::BILLING_YEARLY; // Added billing cycle selection

    public $adminName;

    public $adminEmail;

    public function rules()
    {
        return [
            'tenantName' => 'required|string|max:255',
            'phoneNumber' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:1024',
            'subscriptionTier' => ['required', Rule::in([
                Subscription::PLAN_BASIC,
                Subscription::PLAN_STANDARD,
                Subscription::PLAN_ENTERPRISE,
            ])],
            'billingCycle' => ['required', Rule::in([
                Subscription::BILLING_MONTHLY,
                Subscription::BILLING_YEARLY,
            ])],
            'generatedDomain' => ['required', 'string', Rule::unique('domains', 'domain')],
            'adminName' => 'required|string|max:255',
            'adminEmail' => ['required', 'email', 'max:255'],
        ];
    }

    public function updatedTenantName($value)
    {
        $slug = Str::slug($value);
        $this->generatedDomain = $slug.'.'.config('tenancy.central_domains.0');
        $this->hospitalContactEmail = 'contact@'.$this->generatedDomain;
    }

    public function createTenant()
    {
        $this->validate();

        // 1. Create Tenant
        $tenant = Tenant::create([
            'id' => $this->generatedDomain,
            'name' => $this->tenantName,
            'contact_email' => $this->hospitalContactEmail,
            'phone_number' => $this->phoneNumber,
            'address' => $this->address,
            'subscription_tier' => $this->subscriptionTier,
        ]);

        $tenant->domains()->create(['domain' => $this->generatedDomain]);

        // 2. Setup Context
        $password = Str::password(16);

        $tenant->run(function () {
            $logoPath = $this->logo ? $this->logo->store('logos', 's3') : null;
            // Create Admin
            $user = User::create([
                'name' => $this->adminName,
                'email' => $this->adminEmail,
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);

            // Create Subscription
            $sub = new Subscription;
            $sub->plan = $this->subscriptionTier;
            $sub->billing_cycle = $this->billingCycle;
            $sub->status = Subscription::STATUS_ACTIVE;
            $sub->starts_at = now();

            // Set End Date based on cycle
            $sub->ends_at = match ($this->billingCycle) {
                Subscription::BILLING_MONTHLY => now()->addMonth(),
                Subscription::BILLING_YEARLY => now()->addYear(),
            };

            // Calculate Amount (Model logic)
            $sub->amount = $sub->getPlanAmount();

            // Set Features (Model logic)
            $features = $sub->getDefaultFeatures();
            $sub->features = $features;
            $sub->max_users = $features['max_users'] ?? 0;
            $sub->max_storage = $features['max_storage'] ?? 0;

            $sub->save();
        });

        // 3. Reset & Notify
        $this->reset();
        $this->subscriptionTier = Subscription::PLAN_BASIC;
        $this->billingCycle = Subscription::BILLING_YEARLY;

        LivewireAlert::title('Success')->success()->text('Tenant and Subscription created.')->show();
    }

    /**
     * Helper to get plans for the UI
     */
    public function getPlansProperty()
    {
        // We create temporary instances to get the data defined in your model
        $plans = [
            Subscription::PLAN_BASIC,
            Subscription::PLAN_STANDARD,
            Subscription::PLAN_ENTERPRISE,
        ];

        return collect($plans)->map(function ($plan) {
            $tempSub = new Subscription(['plan' => $plan]);

            return [
                'id' => $plan,
                'name' => $tempSub->getPlanDisplayName(),
                'price' => $tempSub->getPlanAmount(),
                'features' => $tempSub->getDefaultFeatures(),
            ];
        });
    }

    public function render()
    {
        return view('livewire.land-lord.create-tenant', [
            'plans' => $this->plans, // Use the computed property,
        ]);
    }
}
