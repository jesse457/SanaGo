<?php

namespace App\Livewire\Tenants\Doctor;

use App\Models\MedicalRecord as MedicalRecordModel;
use App\Models\Patient;
use App\Models\User;
use App\Services\MedicalRecordService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.doctor')]
class MedicalRecord extends Component
{
    use WithFileUploads;

    // Selections
    public ?Patient $patient = null;

    public ?MedicalRecordModel $medicalRecord = null;

    #[Rule('required|exists:patients,id')]
    public ?int $selectedPatientId = null;

    // Search
    public string $patientQuery = '';

    public Collection $patientResults;

    // Form Fields
    #[Rule('required|string|max:65535')]
    public ?string $complaint = null;

    #[Rule('nullable|string|max:65535')]
    public ?string $diagnosisText = null;

    #[Rule('nullable|string|max:65535')]
    public ?string $clinicalNotes = null; // Maps to general_notes

    // Items
    public Collection $allMedications;

    public array $prescriptionItems = [];

    public Collection $allLabTests;

    public array $labItems = [];

    public Collection $labTechnicianOptions; // Added for the select dropdown

    // Files
    #[Rule(['attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,pdf'])]
    public $attachments = [];

    public $storedAttachments = [];

    public array $attachmentUrls = [];

    public bool $hasUnsavedChanges = false;

    public function mount(MedicalRecordService $service): void
    {
        $this->patientResults = collect();
        $this->allMedications = $service->getAllMedications();
        $this->allLabTests = $service->getAllLabDefinitions();
        // Fetch users who are lab technicians (adjust role logic as per your app)
        $this->labTechnicianOptions = User::where('role', 'lab-technician')->get();
    }

    public function updatedPatientQuery(): void
    {
        $q = trim($this->patientQuery);
        if ($q === '' || strlen($q) < 2) {
            $this->patientResults = collect();

            return;
        }

        $terms = explode(' ', $q);
        $this->patientResults = Patient::query()
            ->where(function ($query) use ($terms) {
                if (count($terms) === 2) {
                    $query->whereBlind('first_name', 'first_name_index', $terms[0])
                        ->whereBlind('last_name', 'last_name_index', $terms[1]);
                } else {
                    foreach ($terms as $term) {
                        $query->orWhereBlind('first_name', 'first_name_index', $term)
                            ->orWhereBlind('last_name', 'last_name_index', $term)
                            ->orWhere('patient_uid', 'ilike', "%$term%");
                    }
                }
            })->limit(10)->get();
    }

    public function selectPatient(int $id, MedicalRecordService $service): void
    {
        $this->selectedPatientId = $id;
        $this->patientQuery = '';
        $this->patientResults = collect();
        $this->updatedSelectedPatientId($service);
    }

    public function updatedSelectedPatientId(MedicalRecordService $service): void
    {
        if (! $this->selectedPatientId) {
            $this->resetContext();

            return;
        }

        $this->patient = Patient::find($this->selectedPatientId);
        $this->medicalRecord = $service->findLatestDraft($this->selectedPatientId, Auth::id());

        if ($this->medicalRecord) {
            $this->hasUnsavedChanges = true;
            $this->complaint = $this->medicalRecord->complaint;
            $this->diagnosisText = $this->medicalRecord->diagnosis_text;
            $this->clinicalNotes = $this->medicalRecord->general_notes;

            $this->prescriptionItems = $service->getDraftPrescriptionItems($this->medicalRecord->id);
            $this->labItems = $service->getDraftLabRequests($this->medicalRecord->id);
        } else {
            $this->resetDraftFields();
        }

        $this->loadStoredAttachments();
    }

    public function addMedication($medicationId)
    {
        $med = $this->allMedications->firstWhere('id', $medicationId);
        if ($med) {
            $this->prescriptionItems[] = [
                'medication_id' => $med->id,
                'name' => $med->name,
                'dosage' => '',
                'frequency' => '',
                'duration' => '',
            ];
            $this->hasUnsavedChanges = true;
        }
    }

    public function removeMedication($index)
    {
        unset($this->prescriptionItems[$index]);
        $this->prescriptionItems = array_values($this->prescriptionItems);
        $this->hasUnsavedChanges = true;
    }

    public function addLabTest($testId)
    {
        $lab = $this->allLabTests->firstWhere('id', $testId);
        if ($lab) {
            $this->labItems[] = [
                'lab_test_definition_id' => $lab->id,
                'test_name' => $lab->test_name,
                'urgency' => 'normal',
                'reason' => '',
                'lab_tech_id' => null,
            ];
            $this->hasUnsavedChanges = true;
        }
    }

    public function removeLabTest($index)
    {
        unset($this->labItems[$index]);
        $this->labItems = array_values($this->labItems);
        $this->hasUnsavedChanges = true;
    }

    public function removeTempAttachment($index)
    {
        unset($this->attachments[$index]);
        $this->attachments = array_values($this->attachments);
    }

    public function saveDraft(MedicalRecordService $service): void
    {
        $this->saveAll($service, false);
    }

    public function saveAndSign(MedicalRecordService $service): void
    {
        $this->saveAll($service, true);
    }

    private function saveAll(MedicalRecordService $service, bool $finalize): void
    {
        $this->validate();

        try {
            // Clean up the data array. lab_tech_id belongs INSIDE the labItems array, not here.
            $data = [
                'id'             => $this->medicalRecord?->id,
                'patient_id'     => $this->selectedPatientId,
                'doctor_id'      => Auth::id(),
                'complaint'      => $this->complaint,
                'diagnosis_text' => $this->diagnosisText,
                'general_notes'  => $this->clinicalNotes,
            ];

            // Ensure prescriptionItems and labItems are passed exactly as they are held in state
            $this->medicalRecord = $service->saveOrUpdate(
                $data,
                $this->attachments,
                $this->prescriptionItems,
                $this->labItems, // This already contains lab_tech_id per item
                $finalize
            );

            $this->attachments = [];

            if ($finalize) {
                $this->resetContext();
                $this->dispatch('alert', ['type' => 'success', 'message' => 'Consultation Finalized and Lab/Prescription sent.']);
            } else {
                $this->updatedSelectedPatientId($service);
                $this->dispatch('alert', ['type' => 'success', 'message' => 'Draft Saved Successfully.']);
            }
        } catch (\Throwable $e) {
            Log::error('Medical Record Error: ' . $e->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Failed to save record.']);
        }
    }

    protected function loadStoredAttachments(): void
    {
        if (! $this->medicalRecord) {
            $this->storedAttachments = [];

            return;
        }
        $this->storedAttachments = $this->medicalRecord->attachments;
        foreach ($this->storedAttachments as $att) {
            $this->attachmentUrls[$att->id] = Storage::disk('s3')->temporaryUrl($att->file_path, now()->addMinutes(30));
        }
    }

    public function removeStoredAttachment(int $id, MedicalRecordService $service): void
    {
        $service->deleteAttachment($id);
        $this->loadStoredAttachments();
    }

    private function resetContext()
    {
        $this->patient = null;
        $this->medicalRecord = null;
        $this->selectedPatientId = null;
        $this->resetDraftFields();
    }

    private function resetDraftFields()
    {
        $this->complaint = null;
        $this->diagnosisText = null;
        $this->clinicalNotes = null;
        $this->prescriptionItems = [];
        $this->labItems = [];
        $this->attachments = [];
        $this->hasUnsavedChanges = false;
    }

    public function render()
    {
        return view('livewire.tenants.doctor.medical-record');
    }
}
