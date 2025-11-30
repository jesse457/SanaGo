<?php

namespace App\Livewire\LandLord;

use App\Models\Tenant;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

// Use the doctor layout for this Livewire component
#[Layout('components.layouts.landlord')]
class ManageTenants extends Component
{
    use WithPagination;

    // Search and data properties
    public $search = '';

    // Modal properties
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $showViewModal = false;

    // Edit form properties
    public $tenantName;
    public $contactEmail;
    public $subscriptionTier = 'Basic';
    public $subscriptionStatus = 'Trialing'; // NEW: Subscription Status
    public $nextRenewalDate;                 // NEW: Next Renewal Date
    public $generatedDomain;

    // Selected tenant for actions
    public $selectedTenant = null;
    public $selectedTenantDomain = null;
    public $viewing = null;
    public $editing = null;

    public function viewTenant($id)
    {
        $tenant = Tenant::find($id);
        if ($tenant) {
            $this->viewing = $tenant->toArray();
            $this->viewing['domain'] = $tenant->domains()->first()?->domain;
            // NEW: Add the new subscription properties for the View Modal
            $this->viewing['subscription_status'] = $tenant->data['subscription_status'] ?? 'N/A';
            $this->viewing['next_renewal_date'] = $tenant->data['next_renewal_date'] ?? 'N/A';

            $this->showViewModal = true;
        }
    }

    public function editTenant($id)
    {
        $tenant = Tenant::with('subscription')->find($id);

        if ($tenant) {
            $this->editing = $tenant->toArray();
            $this->tenantName = $this->editing['name'];
            $this->contactEmail = $this->editing['contact_email'];
            $this->subscriptionTier = $this->editing['subscription_tier'];
            // NEW: Populate the edit form with existing subscription data
            $this->subscriptionStatus = $tenant->data['subscription_status'] ?? 'Trialing';
            $this->nextRenewalDate = $tenant->data['next_renewal_date'] ?? null;

            $this->generatedDomain = $tenant->domains()->first()?->domain;
            $this->showEditModal = true;
        }
    }

    public function viewDeleteTenant($id)
    {
        $tenant = Tenant::find($id);
        if ($tenant) {
            $this->selectedTenant = $tenant->id;
            $this->selectedTenantDomain = $tenant->domains()->first()?->domain;
            $this->showDeleteModal = true;
        }
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->viewing = null;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedTenant = null;
        $this->selectedTenantDomain = null;
    }

    public function saveTenant()
    {
        $this->validate([
            'tenantName' => 'required|string|max:255',
            'contactEmail' => 'required|email|max:255',
            'subscriptionTier' => 'required|in:Basic,Standard,Premium',
            // NEW Validation
            'subscriptionStatus' => 'required|in:Active,Trialing,Canceled,Past Due',
            'nextRenewalDate' => 'nullable|date',
        ]);

        $tenant = Tenant::find($this->editing['id']);
        if ($tenant) {
            $tenant->update([
                'name' => $this->tenantName,
                'contact_email' => $this->contactEmail,
                'subscription_tier' => $this->subscriptionTier,
                // NEW: Update the 'data' column with the new subscription fields
                'data' => array_merge($tenant->data, [
                    'subscription_status' => $this->subscriptionStatus,
                    'next_renewal_date' => $this->nextRenewalDate,
                ]),
            ]);

            $this->dispatch('notify', type: 'success', message: 'Tenant updated successfully!');
            $this->closeEditModal();
            $this->resetPage();
        }
    }

    public function deleteTenant()
    {
        $tenant = Tenant::find($this->selectedTenant);

        if ($tenant) {
            $tenant->delete();
            $this->dispatch('notify', type: 'success', message: 'Tenant deleted successfully!');
            $this->closeDeleteModal();
            $this->resetPage();
        }
    }

    protected $rules = [
        'tenantName' => 'required|string|max:255',
        'contactEmail' => 'required|email|max:255',
        'subscriptionTier' => 'required|in:Basic,Standard,Premium',
        // NEW Rules
        'subscriptionStatus' => 'required|in:Active,Trialing,Canceled,Past Due',
        'nextRenewalDate' => 'nullable|date',
    ];

    public function render()
    {
        // Start a new query on the Tenant model
        $query = Tenant::with('domains');

        // If a search term is provided, add where clauses
        if ($this->search) {
            $term = '%' . strtolower($this->search) . '%';
            $query->where(function ($q) use ($term) {
                // Use a proper WHERE clause for JSONB data
                $q->where('data->name', 'ilike', $term)
                    ->orWhere('data->name', 'ilike', $term)
                    ->orWhereHas('domains', function ($query) use ($term) {
                        $query->where('domain', 'ilike', $term);
                    });
            });
        }

        // Get the results from the database
        $tenants = $query->paginate(10);

        return view('livewire.land-lord.manage-tenants', [
            'tenants' => $tenants,
        ]);
    }

    private function resetForm()
    {
        $this->tenantName = '';
        $this->contactEmail = '';
        $this->subscriptionTier = 'Basic';
        $this->subscriptionStatus = 'Trialing'; // Resetting new property
        $this->nextRenewalDate = null;         // Resetting new property
        $this->generatedDomain = '';
    }
}
