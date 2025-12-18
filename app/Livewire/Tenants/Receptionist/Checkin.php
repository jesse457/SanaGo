<?php

declare(strict_types=1);

namespace App\Livewire\Tenants\Receptionist;

use App\Models\Admission;
use App\Models\Patient;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.receptionist')]
class Checkin extends Component
{
    use WithPagination;

    public string $search = '';

    public int $perPage = 10;

    public bool $showNoPatientsMessage = false;

    public bool $showAdmissionModal = false;

    public ?int $selectedPatientId = null;

    public Collection $recentAdmissions;

    public ?int $selectedAdmissionId = null;

    protected $listeners = ['admissionUpdated' => '$refresh'];

    public function mount()
    {
        $this->recentAdmissions = collect();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Handles the logic for admitting a patient. It will either open a modal
     * with recent admission requests or show a warning if no doctor's approval is present.
     */
    public function admitPatient(int $patientId): void
    {
        $patient = Patient::find($patientId);

        if (! $patient) {
            LivewireAlert::title('Patient Not Found')->warning()->show();

            return;
        }

        // Check for doctor's approval before proceeding.
        if (! $patient->is_admitted_approve) {
            LivewireAlert::title('Invalid Access')->warning()
                ->text('A Doctor must send a request for this patient before admission.')->show();

            return;
        }

        // Load the most recent admissions for the modal.
        $this->recentAdmissions = Admission::where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // Pre-select the latest admission for convenience.
        $latest = $this->recentAdmissions->first();
        if ($latest && ! in_array($latest->status, ['Admitted', 'Discharged'])) {
            $this->selectedAdmissionId = $latest->id;
        } else {
            $this->selectedAdmissionId = null;
        }

        $this->selectedPatientId = $patientId;
        $this->showAdmissionModal = true;
    }

    /**
     * Called when the receptionist selects a specific Admission in modal.
     */
    public function selectAdmission(?int $admissionId): void
    {
        $this->selectedAdmissionId = $admissionId;
    }

    /**
     * Confirms the admission, either by redirecting to a form or directly marking as 'Admitted'.
     */
    public function confirmAdmission(bool $redirectToForm): void
    {
        if (! $this->selectedPatientId) {
            $this->closeModal();
            LivewireAlert::title('No Patient Selected')->text('Please select a patient to admit.')->error()->show();

            return;
        }

        if (! $this->selectedAdmissionId) {
            // No admission selected, so we create a new one.
            $this->closeModal();
            $this->redirect(route('receptionist.admit-patient', ['patient' => $this->selectedPatientId]), navigate: true);

            return;
        }

        $admission = Admission::find($this->selectedAdmissionId);

        if (! $admission) {
            $this->closeModal();
            LivewireAlert::title('Admission Not Found')->text('The selected admission could not be found.')->warning()->show();

            return;
        }

        if (in_array($admission->status, ['Admitted', 'Discharged'])) {
            $this->closeModal();
            LivewireAlert::title('Admission Processed')->text('This admission has already been processed.')->warning()->show();

            return;
        }

        if ($redirectToForm) {
            $this->closeModal();
            $this->redirect(route('receptionist.admit-patient', [

                'admission' => $admission->id,
            ]), navigate: true);
        } else {
            $admission->status = 'Admitted';
            $admission->admitted_by = Auth::id();
            $admission->admission_date = now();
            $admission->save();

            $this->closeModal();

            session()->flash('message', "Admission request #{$admission->id} for {$admission->patient->first_name} {$admission->patient->last_name} has been admitted.");
        }
    }

    private function closeModal(): void
    {
        $this->showAdmissionModal = false;
        $this->selectedPatientId = null;
        $this->selectedAdmissionId = null;
        $this->recentAdmissions = collect();
    }

    public function render()
    {

        $patients = Patient::query()
            ->when($this->search, function ($query, $search) {
                $terms = explode(' ', $search);
                $query->where(function ($q) use ($terms) {
                    if (count($terms) === 2) {
                        $q->WhereBlind('first_name', 'first_name_index', $terms[0])
                            ->WhereBlind('last_name', 'last_name_index', $terms[1]);
                    } else {
                        foreach ($terms as $term) {
                            $q->orWhere('patient_uid', 'like', '%'.$term.'%')
                                ->orWhereBlind('first_name', 'first_name_index', $term)
                                ->orWhereBlind('last_name', 'last_name_index', $term);
                        }
                    }
                });
            })
            ->with(['admissions' => function ($query) {
                $query->latest('created_at')->limit(1);
            }])
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $this->showNoPatientsMessage = $patients->isEmpty() && ! empty($this->search);

        return view('livewire.tenants.receptionist.checkin', [
            'patients' => $patients,
            'userRole' => Auth::user()->role ?? 'receptionist',
        ]);
    }

    public function viewPatientDetails(int $patientId): void
    {
        $admissions = Patient::find($patientId)->load('admissions');
        if (! $admissions) {
            LivewireAlert::title('Error')->error()->text('This patient does not have any admission records.')->show();

            return;
        }
        $this->redirect(route('receptionist.view-admission-details', ['patient' => $patientId]), navigate: true);
    }
}
