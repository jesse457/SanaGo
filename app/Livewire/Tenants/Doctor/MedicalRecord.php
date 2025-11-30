<?php

namespace App\Livewire\Tenants\Doctor;

use App\Models\LabRequest;
use App\Models\LabTestDefinition;
use App\Models\MedicalRecord as MedicalRecordModel;
use App\Models\MedicalRecordAttachment;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.doctor')]
class MedicalRecord extends Component
{
    use WithFileUploads; // Removed UserActivitiesTrait for brevity in snippet, keep if you have it

    public ?Patient $patient = null;

    public ?MedicalRecordModel $medicalRecord = null;

    public Builder|Collection $patientResults;

    #[Rule('required|exists:patients,id')]
    public ?int $selectedPatientId = null;

    public string $patientQuery = '';

    #[Rule('required|string|max:65535')]
    public ?string $complaint = null;

    #[Rule('nullable|string|max:65535')]
    public ?string $diagnosisText = null;

    #[Rule('nullable|string|max:65535')]
    public ?string $clinicalNotes = null;

    // --- Prescription State ---
    public Collection $medicationOptions; // List for Dropdown

    public string $selectedMedicationId = ''; // Selected Dropdown Value

    public array $prescriptionItems = [];

    // --- Lab Request State ---
    public Collection $labTestOptions; // List for Dropdown

    public Collection $labTechnicianOptions; // List of Lab Technicians

    public string $selectedLabTestId = ''; // Selected Dropdown Value

    public array $labItems = [];

    // --- Attachments ---
    #[Rule('nullable|file|max:10240|mimes:jpg,jpeg,png,pdf')]
    public $attachments = null;

    public array $storedAttachments = [];

    public array $attachmentUrls = [];

    public int $uploadProgress = 0;

    public bool $hasUnsavedChanges = false;

    public function mount(): void
    {
        $this->patientResults = collect();

        // Load Dropdown Options
        // Note: If you have thousands of rows, consider using a limit or 'select2' style loading
        $this->medicationOptions = Medication::orderBy('name')->get();
        $this->labTestOptions = LabTestDefinition::orderBy('test_name')->get();
        // Load lab technicians who have the role of 'lab_technician'
        $this->labTechnicianOptions = User::where('role', 'lab-technician')
            ->orderBy('name')->get();
    }

    public function updatedSelectedPatientId(): void
    {
        if (! $this->selectedPatientId) {
            $this->resetContext();

            return;
        }

        $this->patient = Patient::find($this->selectedPatientId);
        if (! $this->patient) {
            $this->selectedPatientId = null;

            return;
        }

        $this->medicalRecord = MedicalRecordModel::with(['attachments'])
            ->where('patient_id', $this->selectedPatientId)
            ->where('doctor_id', Auth::id())
            ->where('finalized', false)
            ->first();

        if ($this->medicalRecord) {
            $this->hasUnsavedChanges = true;
            $this->complaint = $this->medicalRecord->complaint;
            $this->diagnosisText = $this->medicalRecord->diagnosis_text;
            $this->clinicalNotes = $this->medicalRecord->general_notes;

            $this->loadDraftPrescriptions();
            $this->loadDraftLabs();
        } else {
            $this->complaint = null;
            $this->diagnosisText = null;
            $this->clinicalNotes = null;
            $this->prescriptionItems = [];
            $this->labItems = [];
        }

        $this->loadStoredAttachments();
    }

    private function resetContext()
    {
        $this->patient = null;
        $this->medicalRecord = null;
        $this->storedAttachments = [];
        $this->attachmentUrls = [];
        $this->prescriptionItems = [];
        $this->labItems = [];
        $this->complaint = null;
    }

    // --- Prescription Logic (Updated for Dropdown) ---

    public function addMedication()
    {
        if (empty($this->selectedMedicationId)) {
            return;
        }

        // Find the medication object from the collection
        $med = $this->medicationOptions->firstWhere('id', $this->selectedMedicationId);

        if ($med) {
            $this->prescriptionItems[] = [
                'medication_id' => $med->id,
                'name' => $med->name,
                'dosage' => '',
                'frequency' => '',
                'duration' => '',
            ];
            $this->hasUnsavedChanges = true;
            $this->selectedMedicationId = ''; // Reset dropdown
        }
    }

    public function removeMedication($index)
    {
        unset($this->prescriptionItems[$index]);
        $this->prescriptionItems = array_values($this->prescriptionItems);
        $this->hasUnsavedChanges = true;
    }

    protected function loadDraftPrescriptions()
    {
        if (! $this->medicalRecord) {
            return;
        }
        $prescription = Prescription::with('items.medication')
            ->where('consultation_id', $this->medicalRecord->id)
            ->first();

        if ($prescription) {
            foreach ($prescription->items as $item) {
                $this->prescriptionItems[] = [
                    'medication_id' => $item->medication_id,
                    'name' => $item->medication->name ?? 'Unknown Drug',
                    'dosage' => $item->dosage,
                    'frequency' => $item->frequency,
                    'duration' => $item->duration,
                ];
            }
        }
    }

    // --- Lab Logic (Updated for Dropdown) ---

    public function addLabTest()
    {
        if (empty($this->selectedLabTestId)) {
            return;
        }

        // Check for duplicates
        foreach ($this->labItems as $item) {
            if ($item['lab_test_definition_id'] == $this->selectedLabTestId) {
                $this->selectedLabTestId = ''; // Clear selection

                return;
            }
        }

        // Find the lab object from the collection
        $lab = $this->labTestOptions->firstWhere('id', $this->selectedLabTestId);

        if ($lab) {
            $this->labItems[] = [
                'lab_test_definition_id' => $lab->id,
                'test_name' => $lab->test_name, // Changed from 'name' to 'test_name' for consistency
                'urgency' => 'normal',
                'lab_tech_id' => '', // Added lab technician field
                'reason' => '',
            ];
            $this->hasUnsavedChanges = true;
            $this->selectedLabTestId = ''; // Reset dropdown
        }
    }

    public function removeLabTest($index)
    {
        unset($this->labItems[$index]);
        $this->labItems = array_values($this->labItems);
        $this->hasUnsavedChanges = true;
    }

    protected function loadDraftLabs()
    {
        if (! $this->medicalRecord) {
            return;
        }
        $labs = LabRequest::with('testDefinition')
            ->where('consultation_id', $this->medicalRecord->id)
            ->get();

        foreach ($labs as $lab) {
            $this->labItems[] = [
                'lab_test_definition_id' => $lab->lab_test_definition_id,
                'test_name' => $lab->testDefinition->test_name ?? 'Unknown Test', // Changed from 'name' to 'test_name'
                'urgency' => $lab->urgency_level,
                'lab_tech_id' => $lab->lab_tech_id, // Added lab technician field
                'reason' => $lab->reason_for_test,
            ];
        }
    }

    public function saveDraft(): void
    {
        $this->saveAll(false);
    }

    public function saveAndSign(): void
    {
        $this->saveAll(true);
    }

    public function saveAll(bool $finalize = false): void
    {
        $this->validate();

        DB::beginTransaction();
        try {
            // 1. Save Medical Record
            $data = [
                'patient_id' => $this->selectedPatientId,
                'doctor_id' => Auth::id(),
                'complaint' => $this->complaint,
                'diagnosis_text' => $this->diagnosisText,
                'general_notes' => $this->clinicalNotes,
                'finalized' => $finalize,
                'record_type' => 'consultation',
            ];

            if ($this->medicalRecord) {
                $this->medicalRecord->update($data);
                $record = $this->medicalRecord;
            } else {
                $record = MedicalRecordModel::create($data);
            }

            // 2. Save Prescriptions
            if (count($this->prescriptionItems) > 0) {
                $prescription = Prescription::firstOrCreate(
                    ['consultation_id' => $record->id],
                    [
                        'patient_id' => $this->selectedPatientId,
                        'doctor_id' => Auth::id(),
                        'prescription_date' => now(),
                        'status' => 'draft',
                    ]
                );

                if ($finalize) {
                    $prescription->update(['status' => 'prescribed']);
                }

                $prescription->items()->delete();
                foreach ($this->prescriptionItems as $item) {
                    PrescriptionItem::create([
                        'prescription_id' => $prescription->id,
                        'medication_id' => $item['medication_id'],
                        'dosage' => $item['dosage'],
                        'frequency' => $item['frequency'],
                        'duration' => $item['duration'],
                        'quantity_prescribed' => 1,
                    ]);
                }
            } else {
                Prescription::where('consultation_id', $record->id)->delete();
            }

            // 3. Save Lab Requests
            LabRequest::where('consultation_id', $record->id)->delete();
            foreach ($this->labItems as $lab) {
                LabRequest::create([
                    'patient_id' => $this->selectedPatientId,
                    'requested_by_doctor_id' => Auth::id(),
                    'consultation_id' => $record->id,
                    'lab_test_definition_id' => $lab['lab_test_definition_id'],
                    'urgency_level' => $lab['urgency'],
                    'lab_tech_id' => $lab['lab_tech_id'], // Added lab technician ID
                    'reason_for_test' => $lab['reason'],
                    'request_date' => now(),
                    'status' => 'requested',
                ]);
            }

            $this->saveAttachments($record);
            DB::commit();

            if ($finalize) {
                $this->resetContext();
                $this->selectedPatientId = null;
                LivewireAlert::title('Success')->text('Consultation finalized.')->success()->show();
            } else {
                $this->updatedSelectedPatientId();
                LivewireAlert::title('Saved')->text('Draft saved.')->success()->show();
            }

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Save failed: '.$e->getMessage());
            LivewireAlert::error('Failed to save record.')->show();
        }
    }

    public function updatedPatientQuery(): void
    {
        $q = trim($this->patientQuery);
        if ($q === '') {
            $this->patientResults = collect();

            return;
        }

        $doctorId = Auth::id();
        $terms = explode(' ', $this->patientQuery);
        $this->patientResults = Patient::query()
            // Optional: Scope search to patients seen by the current doctor for relevance
            ->whereHas('appointments', fn ($b) => $b->where('doctor_id', $doctorId))
            ->where(function ($patientQuery) use ($terms) {
                if (count($terms) === 2) {
                    // Exact first + last name search (most efficient)
                    $patientQuery->whereBlind('first_name', 'first_name_index', $terms[0])
                        ->whereBlind('last_name', 'last_name_index', $terms[1]);
                } else {
                    // Single term or multiple fragments: match against indexed fields
                    foreach ($terms as $term) {
                        $patientQuery->orWhereBlind('first_name', 'first_name_index', $term)
                            ->orWhereBlind('last_name', 'last_name_index', $term)
                            ->orWhere('patient_uid', 'like', "%$term%");
                    }
                }
            })
            ->limit(12)
            ->get();
    }

    public function selectPatient(int $id): void
    {
        $this->selectedPatientId = $id;
        $this->patientQuery = '';
        $this->patientResults = collect();
        $this->updatedSelectedPatientId();
    }

    public function updatedAttachments(): void
    {
        $this->validateOnly('attachments');
        $this->hasUnsavedChanges = true;
    }

    public function removeAttachment(): void
    {
        $this->attachments = null;
        $this->hasUnsavedChanges = true;
    }

    protected function saveAttachments(MedicalRecordModel $record): void
    {
        if (! $this->attachments) {
            return;
        }
        $path = $this->attachments->store('medical_record', 's3');
        MedicalRecordAttachment::create([
            'medical_record_id' => $record->id,
            'file_path' => $path,
            'file_name' => $this->attachments->getClientOriginalName(),
            'file_type' => $this->attachments->getClientMimeType(),
        ]);
        $this->attachments = null;
        $this->loadStoredAttachments();
    }

    protected function loadStoredAttachments(): void
    {
        if ($this->medicalRecord === null) {
            $this->storedAttachments = [];
            $this->attachmentUrls = [];

            return;
        }

        $this->storedAttachments = $this->medicalRecord->attachments->all();
        $this->attachmentUrls = $this->medicalRecord->attachments->map(
            fn ($a) => Storage::disk('s3')->temporaryUrl($a->file_path, now()->addMinutes(20))
        )->all();
    }

    public function removeStoredAttachment(int $index): void
    {
        if (! isset($this->storedAttachments[$index])) {
            return;
        }
        $this->storedAttachments[$index]->delete();
        $this->loadStoredAttachments();
    }

    public function render()
    {
        return view('livewire.tenants.doctor.medical-record');
    }
}
