<?php

namespace App\Livewire\LandLord;

use App\Mail\UserInvitationMail;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Throwable;

#[Layout('components.layouts.landlord')]
class CreateTenant extends Component
{
    use WithFileUploads;

    public $tenantName;
    public $phoneNumber;
    public $address;
    public $logo = null;
    public $tempUser; // To hold the created user for email sending
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
            'generatedDomain' => [
                'required',
                'string',
                'lowercase',
                Rule::unique('domains', 'domain'), // Check the domains table

            ],
            'adminName' => 'required|string|max:255',
            'adminEmail' => ['required', 'email', 'max:255'],
        ];
    }

    public function updatedTenantName($value)
    {
        // Auto-generate domain: myhospital.sanago.site
        $slug = Str::slug($value);
        $this->generatedDomain = $slug . '.' . config('tenancy.central_domains.0');
        $this->hospitalContactEmail = 'contact@' . $this->generatedDomain;
    }

 public function createTenant()
{
    $this->validate();

    // Set static password for testing as requested
    $password = 'password';

    // Use references to capture data inside the transaction for use outside
    $token = null;

    try {
        $tenant = DB::transaction(function () use ($password, &$token) {
            // 1. Create Tenant (Central)
            $tenant = Tenant::create([
                'name' => $this->tenantName,
                'contact_email' => $this->hospitalContactEmail,
                'phone_number' => $this->phoneNumber,
                'address' => $this->address,
                'subscription_tier' => $this->subscriptionTier,
            ]);

            // 2. Create Domain (Central)
            $tenant->domains()->create([
                'domain' => $this->generatedDomain
            ]);

            // 3. Create Tenant-Specific Data (Single DB scope)
            $tenant->run(function () use ($password, &$token) {
                $user = User::create([
                    'name' => $this->adminName,
                    'email' => $this->adminEmail,
                    'password' => Hash::make($password),
                    'role' => 'admin',
                ]);

                // Generate Password Reset Token while inside the tenant context
                $token = Password::broker()->createToken($user);

                // Create Initial Subscription
                $sub = new Subscription();
                $sub->plan = $this->subscriptionTier;
                $sub->billing_cycle = $this->billingCycle;
                $sub->status = Subscription::STATUS_ACTIVE;
                $sub->starts_at = now();
                $sub->ends_at = ($this->billingCycle === Subscription::BILLING_MONTHLY) ? now()->addMonth() : now()->addYear();
                $sub->amount = $sub->getPlanAmount();
                $sub->features = $sub->getDefaultFeatures();
                $sub->save();

                // Assign to component property for access outside transaction
                $this->tempUser = $user;
            });

            return $tenant;
        });

        // --- OUTSIDE TRANSACTION ---
        // This prevents the "Poisoned Transaction" error if S3 or Mail fails.

        // 4. Handle Logo Upload
        if ($this->logo) {
            try {
                $logoPath = $this->logo->store('logos', 's3');
                $tenant->update(['logo' => $logoPath]);
            } catch (Throwable $e) {
                Log::warning('Logo upload failed, but tenant was created.', ['error' => $e->getMessage()]);
            }
        }

        // 5. Send Invitation Email
        // We pass the string variables directly to avoid relationship lookup errors
        if ($this->tempUser && $token) {
            Mail::to($this->tempUser->email)->queue(new UserInvitationMail(
                $this->tempUser,
                $token,
                $this->generatedDomain,
                $this->tenantName
            ));
        }

        $this->reset(['tenantName', 'phoneNumber', 'address', 'logo', 'generatedDomain', 'adminName', 'adminEmail']);
        LivewireAlert::title('Tenant Created')->success()->text('Invitation sent to ' . $this->adminEmail);

    } catch (Throwable $e) {
        Log::error('TENANT_CREATION_FAILED', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString() // Added full trace for easier production debugging
        ]);

        $errorMessage = str_contains($e->getMessage(), 'SQLSTATE[23505]')
            ? 'This domain or email is already taken.'
            : 'Creation failed: ' . $e->getMessage();

        LivewireAlert::title('Error')->error()->text($errorMessage);
    }
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
