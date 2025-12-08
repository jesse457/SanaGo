<?php

namespace App\Livewire\LandLord;

use App\Mail\UserInvitationMail;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB; // [FIX] Added for transaction
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
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
    public $billingCycle = Subscription::BILLING_YEARLY;
    public $adminName;
    public $adminEmail;

    public function rules()
    {
        return [
            'tenantName' => 'required|string|max:255',
            'phoneNumber' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:1024',
            'subscriptionTier' => ['required', Rule::in([Subscription::PLAN_BASIC, Subscription::PLAN_STANDARD, Subscription::PLAN_ENTERPRISE])],
            'billingCycle' => ['required', Rule::in([Subscription::BILLING_MONTHLY, Subscription::BILLING_YEARLY])],
            'generatedDomain' => ['required', 'string', Rule::unique('domains', 'domain')],
            'adminName' => 'required|string|max:255',
            'adminEmail' => ['required', 'email', 'max:255'],
        ];
    }

    public function updatedTenantName($value)
    {
        // Auto-generate domain based on name
        $slug = Str::slug($value);
        $this->generatedDomain = $slug . '.' . config('tenancy.central_domains.0');
        $this->hospitalContactEmail = 'contact@' . $this->generatedDomain;
    }

    public function createTenant()
    {
        $this->validate();

        // Generate a random password for the new Admin
        $password = Str::password(16);

        // [FIX] Start Transaction to ensure Tenant, Domain, and User are all created or none are
        DB::transaction(function () use ($password) {

            // 1. Create Tenant (Central DB)
            $tenant = Tenant::create([
                'id' => $this->generatedDomain,
                'name' => $this->tenantName,
                'contact_email' => $this->hospitalContactEmail,
                'phone_number' => $this->phoneNumber,
                'address' => $this->address,
                'subscription_tier' => $this->subscriptionTier,
                // Logo is handled below
            ]);

            // 2. Create Domain (Central DB)
            $tenant->domains()->create(['domain' => $this->generatedDomain]);

            // 3. Handle Logo Upload (S3)
            // [FIX] Upload file and UPDATE the tenant record
            if ($this->logo) {
                $logoPath = $this->logo->store('logos', 's3');
                $tenant->update(['logo' => $logoPath]);
            }

            // 4. Setup Tenant Context (Switch to Tenant DB)
            $tenant->run(function () use ($password) {
                // Create Admin User
                $user = User::create([
                    'name' => $this->adminName,
                    'email' => $this->adminEmail,
                    // [FIX] Use the generated $password, not hardcoded 'password'
                    'password' => Hash::make($password),
                    'role' => 'admin',
                ]);
                $token = Password::broker()->createToken($user);
                // 3. Send Email
                // [FIX] Queuing the modern email we created
                Mail::to($user->email)->queue(new UserInvitationMail($user, $token));

                // Create Subscription Record
                $sub = new Subscription();
                $sub->plan = $this->subscriptionTier;
                $sub->billing_cycle = $this->billingCycle;
                $sub->status = Subscription::STATUS_ACTIVE;
                $sub->starts_at = now();

                // Logic to set end date
                $sub->ends_at = match ($this->billingCycle) {
                    Subscription::BILLING_MONTHLY => now()->addMonth(),
                    Subscription::BILLING_YEARLY => now()->addYear(),
                };

                // Model Logic methods
                $sub->amount = $sub->getPlanAmount();
                $features = $sub->getDefaultFeatures();
                $sub->features = $features;
                $sub->max_users = $features['max_users'] ?? 0;
                $sub->max_storage = $features['max_storage'] ?? 0;

                $sub->save();
            });

            // Optional: You should probably email the Landlord Admin here with credentials too!
        });

        // 5. Reset & Notify
        $this->reset();
        // Reset defaults
        $this->subscriptionTier = Subscription::PLAN_BASIC;
        $this->billingCycle = Subscription::BILLING_YEARLY;

        LivewireAlert::title('Success')->success()->text('Tenant created successfully.')->show();
    }

    public function getPlansProperty()
    {
        $plans = [Subscription::PLAN_BASIC, Subscription::PLAN_STANDARD, Subscription::PLAN_ENTERPRISE];

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
            'plans' => $this->plans
        ]);
    }
}
