<?php

namespace App\Livewire\Tenants\Receptionist;

use App\Models\Patient;
use App\Traits\UserActivitiesTrait;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.receptionist')]
class Patients extends Component
{
    use UserActivitiesTrait,WithPagination;

    #[Url]
    public $search = '';

    // Modal / edit state
    public $showEditModal = false;

    public ?int $editingPatientId = null;

    // Editable fields (mirror Patient::$fillable)
    public $patient_uid;

    public $first_name;

    public $last_name;

    public $age; // YYYY-MM-DD

    public $gender;

    public $blood_group;

    public $phone;

    public $email;

    public $address;

    protected $listeners = [
        'deletePatient' => 'deletePatient',
        'cancelDelete' => 'cancelDelete',
    ];

    protected function rules(): array
    {
        return [
            'patient_uid' => 'required|string|max:191|unique:patients,patient_uid'.($this->editingPatientId ? (','.$this->editingPatientId) : ''),
            'first_name' => 'required|string|max:191',
            'last_name' => 'nullable|string|max:191',
            'age' => 'nullable|integer',
            'gender' => 'nullable|in:male,female,other',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:191',
            'address' => 'nullable|string|max:1000',
        ];
    }

    public function render()
    {
        $query = Patient::query();

        if ($this->search) {

            $terms = explode(' ', $this->search); // split by spaces
            // Always allow partial matching on non-encrypted patient_uid
            $query->where(function ($q) use ($terms) {

                // For encrypted searchable fields we can only do exact matches via blind indexes.
                // Compute blind indexes and add OR where clauses where appropriate.
                try {
                    if (count($terms) === 2) {
                        $q->WhereBlind('first_name', 'first_name_index', $terms[0]);
                        $q->WhereBlind('last_name', 'last_name_index', $terms[1]);
                    } else {
                        // Single term or multiple fragments: match against indexed fields
                        foreach ($terms as $term) {
                            $q->orWhereBlind('first_name', 'first_name_index', $term)
                                ->orWhereBlind('last_name', 'last_name_index', $term)
                                ->orWhere('patient_uid', 'like', "%$term%");
                        }
                    }
                } catch (\Throwable $e) {
                    // If blind index generation fails, just skip those checks.
                    // Optionally log $e for debugging.
                }
            });
        }

        $patients = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.tenants.receptionist.patients', [
            'patients' => $patients,
        ]);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Open edit modal and load patient data into component properties.
     */
    public function openEditModal(int $patientId): void
    {
        $patient = Patient::find($patientId);

        if (! $patient) {
            LivewireAlert::title('Not found')
                ->text('Patient not found.')
                ->error()
                ->toast()
                ->position('top-end')
                ->timer(3000)
                ->show();

            return;
        }

        $this->editingPatientId = $patient->id;
        $this->patient_uid = $patient->patient_uid;
        $this->first_name = $patient->first_name;
        $this->last_name = $patient->last_name;
        $this->age = $patient->age;
        $this->gender = $patient->gender;
        $this->phone = $patient->phone;
        $this->email = $patient->email;
        $this->address = $patient->address;

        $this->resetValidation();
        $this->showEditModal = true;
    }

    /**
     * Cancel editing and close modal
     */
    public function cancelEdit(): void
    {
        $this->showEditModal = false;
        $this->editingPatientId = null;
        $this->resetEditableFields();
        $this->resetValidation();
    }

    /**
     * Save patient changes (update existing patient).
     */
    public function savePatient(): void
    {
        // Validate; rules() uses $this->editingPatientId to skip unique constraint
        $this->validate();

        if (! $this->editingPatientId) {
            LivewireAlert::title('Update failed')
                ->text('No patient selected to update.')
                ->error()
                ->show();

            return;
        }

        try {
            $patient = Patient::findOrFail($this->editingPatientId);

            $patient->update([
                'patient_uid' => $this->patient_uid,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'age' => $this->age ?: null,
                'gender' => $this->gender,
                'blood_group' => $this->blood_group,
                'phone' => $this->phone,
                'email' => $this->email,
                'address' => $this->address,
            ]);
            $this->logActivity(
                'Patient_Updated',
                'Updated patient '.$patient->full_name,
                [
                    'patient_id' => $patient->id,
                ]
            );
            LivewireAlert::title('Patient updated')
                ->text('Patient record updated successfully.')
                ->success()
                ->show();

            $this->showEditModal = false;
            $this->editingPatientId = null;
            $this->resetEditableFields();
            $this->resetValidation();
            $this->resetPage();
        } catch (\Exception $e) {
            Log::error('Failed to update patient: '.$e->getMessage(), ['patient_id' => $this->editingPatientId]);

            LivewireAlert::title('Update failed')
                ->text('Unable to update patient. Try again later.')
                ->error()
                ->show();
        }
    }

    /**
     * Helper to clear editable fields
     */
    protected function resetEditableFields(): void
    {
        $this->patient_uid = null;
        $this->first_name = null;
        $this->last_name = null;
        $this->age = null;
        $this->gender = null;
        $this->blood_group = null;
        $this->phone = null;
        $this->email = null;
        $this->address = null;
    }
}
