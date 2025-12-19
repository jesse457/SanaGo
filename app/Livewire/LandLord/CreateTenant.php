<?php

namespace App\Livewire\LandLord;

use App\Mail\UserInvitationMail;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB; // [FIX] Added for transaction
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
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
        // 1. Validate inputs (this prevents simple unique errors from poisoning the DB)
        $this->validate();

        $password = Str::password(16);

        try {
            // 2. Wrap the entire process in a transaction on the Central Connection
            DB::connection(config('tenancy.database.central_connection'))->transaction(function () use ($password) {

                // Create the Tenant
                $tenant = Tenant::create([
                    'id' => $this->generatedDomain,
                    'name' => $this->tenantName,
                    'contact_email' => $this->hospitalContactEmail,
                    'subscription_tier' => $this->subscriptionTier,
                    // add other fields...
                ]);

                // Create the Domain
                $tenant->domains()->create(['domain' => $this->generatedDomain]);

                // Handle Logo
                if ($this->logo) {
                    $logoPath = $this->logo->store('logos', 's3');
                    $tenant->update(['logo' => $logoPath]);
                }

                // 3. Switch to Tenant Context
                $tenant->run(function () use ($password) {
                    $user = User::create([
                        'name' => $this->adminName,
                        'email' => $this->adminEmail,
                        'password' => Hash::make($password),
                        'role' => 'admin',
                    ]);

                    $token = Password::broker()->createToken($user);

                    // Queue mail to avoid timeout/blocking the transaction
                    Mail::to($user->email)->queue(new UserInvitationMail($user, $token));

                    // Subscription setup...
                    $sub = new Subscription();
                    $sub->plan = $this->subscriptionTier;
                    $sub->save();
                });
            });

            // 4. If we reach here, everything worked
            $this->reset();
            LivewireAlert::title('Success')->success()->text('Tenant created successfully.')->show();

        } catch (Throwable $e) {
            // ============================================================
            // 5. THE SPECIFIC ERROR LOGGING
            // ============================================================
            Log::error('TENANT_CREATION_FAILED', [
                'message' => $e->getMessage(),      // The actual error (e.g. "Table not found")
                'file'    => $e->getFile(),         // Where it happened
                'line'    => $e->getLine(),         // Exact line number
                'domain'  => $this->generatedDomain,// The domain being created
                'sql'     => method_exists($e, 'getSql') ? $e->getSql() : 'N/A', // If it's a DB error
            ]);

              LivewireAlert::title('Error')->error()->text('Tenant creation failed.')->show();
         
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
