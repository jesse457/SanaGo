<?php

namespace App\Livewire\Tenants\Doctor;

use App\Traits\UserActivitiesTrait;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.doctor')]
class DoctorLabRequest extends Component
{
    use UserActivitiesTrait, WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public int $perPage = 10;

    protected array $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    protected $listeners = ['refreshLabRequests' => '$refresh'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter']);
        $this->resetPage();
    }

    /**
     * Compute the list of lab requests based on search and filters.
     * The search logic for patient names has been adjusted to be less restrictive (OR-based)
     * to reliably match encrypted tokens.
     */
    #[Computed]
    public function requests()
    {
        return app(\App\Services\LabService::class)->getLabRequestsQuery([
            'search' => $this->search,
            'status' => $this->statusFilter,
        ])->paginate($this->perPage);
    }

    /**
     * Render the component view.
     */
    public function render()
    {
        // The view now accesses the computed property using $this->requests
        return view('livewire.tenants.doctor.doctor-lab-request');
    }
}
