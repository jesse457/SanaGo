<?php

namespace App\Livewire\Tenants\Doctor;

use App\Models\Patient as PatientModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

// Use the doctor layout for this Livewire component
#[Layout('components.layouts.doctor')]
class Patient extends Component
{
    use WithPagination;

    // Search input from the user
    public string $search = '';

    // Sorting option selected by the user
    public string $sortBy = 'name-asc';

    public string $filterStatus = '';

    // Persist query string for search and sortBy
    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'name-asc'],
    ];

    // Reset pagination when search input changes
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Computed property to fetch patients for the authenticated doctor
    #[Computed(persist: true)]
    public function patients()
    {
        $doctorId = Auth::id(); // Get the current doctor's ID
        $terms = explode(' ', $this->search); // split by spaces
        return PatientModel::query()
            // Only patients with appointments or admissions for this doctor
            ->where(function ($q) use ($doctorId) {
                $q->whereHas('appointments', function ($q) use ($doctorId) {
                    $q->where('doctor_id', $doctorId);
                })->orWhereHas('admissions', function ($q) use ($doctorId) {
                    $q->where('doctor_id', $doctorId);
                });
            })

            // Apply search filter if search input is present
            ->when(
                $this->search,
                fn($q) => $q
                    ->where(function ($q) use ($terms) {
                        if (count($terms) == 2) {
                            $q->whereBlind('first_name', 'first_name_index', $terms[0])
                                ->WhereBlind('last_name', 'last_name_index', $terms[1]);
                        } else {
                            foreach ($terms as $term) {
                                $q->WhereBlind('first_name', 'first_name_index', $term)
                                    ->orWhereBlind('last_name', 'last_name_index', $term)
                                    ->orWhere('patient_uid', 'ILIKE', "%{$this->search}%");
                            }
                        }
                    })
            )
            // Apply sorting based on selected option
            ->orderBy(
                match ($this->sortBy) {
                    'name-asc' => 'last_name',
                    'name-desc' => 'last_name',
                    'dob-asc' => 'dob',
                    'dob-desc' => 'dob',
                    default => 'last_name'
                },
                str_ends_with($this->sortBy, '-desc') ? 'desc' : 'asc'
            )
            // Eager-load relationships to avoid N+1 queries
            ->with(['appointments', 'admissions'])
            // Paginate results (15 per page)
            ->paginate(15);
    }

    // Render the Livewire component view
    public function render()
    {
        return view('livewire.tenants.doctor.patient', [
            'patients' => $this->patients(),
            // Sorting options for the dropdown/select
            'sortOpts' => [
                'name-asc' => 'Name (A-Z)',
                'name-desc' => 'Name (Z-A)',
                'dob-asc' => 'DOB (Oldest First)',
                'dob-desc' => 'DOB (Youngest First)',
            ],
        ]);
    }
}
