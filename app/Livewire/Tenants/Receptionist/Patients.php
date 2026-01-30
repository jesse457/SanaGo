<?php

namespace App\Livewire\Tenants\Receptionist;

use App\Services\PatientService;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.receptionist')]
class Patients extends Component
{
    use WithPagination; // UserActivitiesTrait removed as Service handles logging

    #[Url]
    public string $search = '';

    // Modal / Edit State
    public bool $showEditModal = false;

    public ?int $editingPatientId = null;

    // Editable fields
    public $patient_uid;

    public $first_name;

    public $last_name;

    public $age;

    public $gender;

    public $blood_group;

    public $phone;

    public $email;

    public $address;

    protected $listeners = [
        'deletePatient' => 'deletePatient',
        'refreshComponent' => '$refresh',
    ];

    /**
     * Validation Rules
     */
    protected function rules(): array
    {
        return [
            'patient_uid' => 'required|string|max:191|unique:patients,patient_uid'.($this->editingPatientId ? (','.$this->editingPatientId) : ''),
            'first_name' => 'required|string|max:191',
            'last_name' => 'required|string|max:191',
            'age' => 'nullable|integer|min:0|max:150',
            'gender' => 'required|in:male,female,other',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:191',
            'address' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Renders the patient list using the shared Service.
     */
    public function render(PatientService $service)
    {
        // Use the Service's Builder-based search
        $patients = $service->search($this->search)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.tenants.receptionist.patients', [
            'patients' => $patients,
        ]);
    }

    /**
     * Reset pagination when search query changes.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Load patient data into the modal using the Service.
     */
    public function openEditModal(int $patientId, PatientService $service): void
    {
        try {
            $patient = $service->findPatient($patientId);

            if (! $patient) {
                $this->dispatch('notify', ['type' => 'error', 'message' => 'Patient record not found.']);

                return;
            }

            $this->editingPatientId = $patient->id;
            $this->patient_uid = $patient->patient_uid;
            $this->first_name = $patient->first_name;
            $this->last_name = $patient->last_name;
            $this->age = $patient->age;
            $this->gender = $patient->gender; // This will be lowercase (male/female)
            $this->phone = $patient->phone;
            $this->email = $patient->email;
            $this->address = $patient->address;

            $this->resetValidation();
            $this->showEditModal = true;

        } catch (\Throwable $e) {
            Log::error('Edit Load Error: '.$e->getMessage());
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Failed to load patient data.']);
        }
    }

    /**
     * Save patient changes using the Service.
     */
    public function savePatient(PatientService $service): void
    {
        $this->validate();

        if (! $this->editingPatientId) {
            return;
        }

        try {
            $patient = $service->findPatient($this->editingPatientId);

            if (! $patient) {
                throw new \Exception('Patient not found.');
            }

            // Delegate update logic and logging to the Service
            $service->updatePatient($patient, [
                'patient_uid' => $this->patient_uid,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'age' => $this->age ?: null,
                'gender' => strtolower($this->gender),
                'phone' => $this->phone,
                'email' => $this->email,
                'address' => $this->address,
            ]);

            LivewireAlert::title('Success')->text('Patient updated successfully.')->success()->show();

            $this->cancelEdit(); // Close modal and reset fields

        } catch (\Exception $e) {
            Log::error('Patient Update Failed: '.$e->getMessage());
            LivewireAlert::title('Error')->text('Unable to update patient. Please try again.')->error()->show();
        }
    }

    /**
     * Handle Patient Deletion
     */
    public function deletePatient(int $id, PatientService $service)
    {
        try {
            $patient = $service->findPatient($id);

            if ($patient) {
                // Note: You might want to add a delete method to your Service
                // to handle logging 'deleted' activity there too.
                // For now, doing it directly here, but ideally service->delete($patient)
                $patient->delete();

                LivewireAlert::title('Success')->text('Patient record deleted.')->success()->show();
            } else {
                LivewireAlert::title('Error')->text('Patient not found.')->error()->show();
            }
        } catch (\Exception $e) {
            Log::error('Patient Delete Failed: '.$e->getMessage());
            LivewireAlert::title('Error')->text('Could not delete record.')->error()->show();
        }
    }

    /**
     * Reset Modal State
     */
    public function cancelEdit(): void
    {
        $this->showEditModal = false;
        $this->editingPatientId = null;
        $this->resetEditableFields();
        $this->resetValidation();
    }

    protected function resetEditableFields(): void
    {
        $this->reset([
            'patient_uid', 'first_name', 'last_name', 'age',
            'gender', 'phone', 'email', 'address',
        ]);
    }
}
