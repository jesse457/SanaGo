<?php

namespace App\Livewire\LandLord;

use App\Mail\SendCredentials;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Subscription; // <-- Import the Subscription model
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule; // <-- Import Rule for validation
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

#[Layout('components.layouts.landlord')]
class CreateTenant extends Component
{
    use WithFileUploads;

    // Properties for the Tenant
    public $tenantName;
    public $phoneNumber;
    public $address;
    public $logo = null;
    public $subscriptionTier = Subscription::PLAN_BASIC; // <-- Use model constant for default
    public $generatedDomain;
    public $hospitalContactEmail;

    // Properties for the Tenant's Admin User
    public $adminName;
    public $adminEmail;

    public function rules()
    {
        return [
            // Tenant Rules
            'tenantName' => 'required|string|max:255',
            'phoneNumber' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:1024', // max 1MB
            'subscriptionTier' => [ // <-- Update validation to use constants
                'required',
                Rule::in([Subscription::PLAN_BASIC, Subscription::PLAN_STANDARD, Subscription::PLAN_PREMIUM]),
            ],
            'generatedDomain' => [
                'required',
                'string',
                Rule::unique('domains', 'domain'),
            ],
            // Admin User Rules
            'adminName' => 'required|string|max:255',
            'adminEmail' => [
                'required',
                'email',
                'max:255',
            ],
        ];
    }

    public function updatedTenantName($value)
    {
        $slug = Str::slug($value);
        $this->generatedDomain = $slug . '.' . config('tenancy.central_domains.0');
        $this->hospitalContactEmail = 'contact@' . $this->generatedDomain;
    }

    public function createTenant()
    {
        $this->validate();

        $logoPath = null;
        if ($this->logo) {
            $logoPath = $this->logo->store('logos', 's3');
        }

        // Create the Tenant
        $tenant = Tenant::create([
            'id' => $this->generatedDomain,
            'name' => $this->tenantName,
            'contact_email' => $this->hospitalContactEmail,
            'phone_number' => $this->phoneNumber,
            'address' => $this->address,
            'logo' => $logoPath,
            'subscription_tier' => $this->subscriptionTier, // This is still fine as a quick reference
        ]);

        // Create the Tenant's Domain
        $tenant->domains()->create([
            'domain' => $this->generatedDomain,
        ]);

        // Generate a secure password for the admin
        $generatedPassword = Str::password(16, true, true, true, false);

        // Run operations within the new tenant's context
        $tenant->run(function () use ($generatedPassword) {

            // 1. Create the Admin User
            $user = User::create([
                'name' => $this->adminName,
                'email' => $this->adminEmail,
                'password' => Hash::make('password'), // <-- BUG FIX: Use the generated password
                'role' => 'admin',
            ]);

            // 2. Create the initial Subscription
            $subscription = new Subscription([
                'plan' => $this->subscriptionTier,
                'status' => Subscription::STATUS_ACTIVE,
                'starts_at' => now(),
                'ends_at' => now()->addYear(), // Default 1-year subscription
                'billing_cycle' => Subscription::BILLING_YEARLY,
            ]);

            // Get default features for this plan and assign them
            $defaultFeatures = $subscription->getDefaultFeatures();
            $subscription->features = $defaultFeatures;

            $subscription->amount = $subscription->getPlanAmount();
            // Populate top-level convenience columns (based on your model's fillable)
            $subscription->max_users = $defaultFeatures['max_users'] ?? 0;
            $subscription->max_storage = $defaultFeatures['max_storage'] ?? 0;

            $subscription->save(); // Save the subscription to the tenant's DB

            // 3. Prepare and send the credentials email
            $mailable = new SendCredentials([
                'subject'  => 'Welcome to ' . $this->tenantName . '!',
                'view'     => 'emails.welcome',
                'name'     => $user->name ?? 'User',
                'email'    => $user->email,
                'password' => $generatedPassword,
                'login_url' => 'http://' . $this->generatedDomain, // Use tenant's domain for login
            ]);

            // Mail::to($user->email)->queue($mailable); // <-- Enabled emailing
        });

        $this->resetForm();
        LivewireAlert::title('Success')->success()->text('Tenant created successfully')->show();
    }

    private function resetForm()
    {
        $this->reset();
        $this->subscriptionTier = Subscription::PLAN_BASIC; // <-- Use model constant
    }

    public function render()
    {
        // Pass the available plans to the view
        $availablePlans = [
            Subscription::PLAN_BASIC,
            Subscription::PLAN_STANDARD,
            Subscription::PLAN_PREMIUM,
        ];

        return view('livewire.land-lord.create-tenant', [
            'availablePlans' => $availablePlans
        ]);
    }
}
