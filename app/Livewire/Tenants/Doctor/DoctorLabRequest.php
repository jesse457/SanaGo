<?php

namespace App\Livewire\Tenants\Doctor;

use App\Models\LabRequest;
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
        $search = trim($this->search);
        // Split search input into individual terms, filtering out any empty strings
        $terms = $search ? array_filter(explode(' ', $search)) : [];

        return LabRequest::query()
            ->when($search, function ($query) use ($search, $terms) {

                // Apply search filter using a top-level OR group for all search types
                $query->where(function ($q) use ($search, $terms) {

                    // 1. Search by Patient UID (Partial match using ILIKE)
                    // Use a WHERE clause to start the main OR group
                    $q->whereHas('patient', function ($patientQuery) use ($search) {
                        $patientQuery->where('patient_uid', 'ILIKE', "%{$search}%");
                    });

                    // 2. Search by Test Name (Partial match using ILIKE) - Combined with OR
                    $q->orWhereHas('testDefinition', function ($testQuery) use ($search) {
                        $testQuery->where('test_name', 'ILIKE', "%{$search}%");
                    });

                    // 3. Search by Patient Name (Encrypted Fields) - Combined with OR
                    $q->orWhereHas('patient', function ($patientQuery) use ($terms) {
                        // This uses OR logic across all search terms and name fields to maximize matches.
                        $patientQuery->where(function ($nameQuery) use ($terms) {
                            $isFirstTerm = true;
                            foreach ($terms as $term) {
                                // Group the First Name OR Last Name search for the current term
                                $nestedClosure = function ($termQuery) use ($term) {
                                    $termQuery->whereBlind('first_name', 'first_name_index', $term)
                                        ->orWhereBlind('last_name', 'last_name_index', $term);
                                };

                                // Apply WHERE for the first term, and OR WHERE for all subsequent terms.
                                if ($isFirstTerm) {
                                    $nameQuery->where($nestedClosure);
                                    $isFirstTerm = false;
                                } else {
                                    $nameQuery->orWhere($nestedClosure);
                                }
                            }
                        });
                    });

                }); // End of main OR group

            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            // Eager load necessary relationships
            ->with(['patient', 'testDefinition', 'doctor'])
            ->orderBy('request_date', 'desc')
            ->paginate($this->perPage);
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
