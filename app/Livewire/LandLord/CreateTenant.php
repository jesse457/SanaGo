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
