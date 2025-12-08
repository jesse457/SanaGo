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
    use WithFileUploads;

    public ?Patient $patient = null;
    public ?MedicalRecordModel $medicalRecord = null;
    public Builder|Collection $patientResults;

    #[Rule('required|exists:patients,id')]
    public ?int $selectedPatientId = null;
    public string $patientQuery = '';

    // Clinical Data
    #[Rule('required|string|max:65535')]
    public ?string $complaint = null;

    #[Rule('nullable|string|max:65535')]
    public ?string $diagnosisText = null;

    #[Rule('nullable|string|max:65535')]
    public ?string $clinicalNotes = null;

    // Prescriptions
    public Collection $medicationOptions;
    public string $selectedMedicationId = '';
    public array $prescriptionItems = [];

    // Lab Requests
    public Collection $labTestOptions;
    public Collection $labTechnicianOptions;
    public string $selectedLabTestId = '';
    public array $labItems = [];

    // --- FIX: Change to Array for Multiple Files ---
    #[Rule(['attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,pdf'])]
    public $attachments = []; // Initialize as array

    public array $storedAttachments = [];
    public array $attachmentUrls = []; // Keys match storedAttachments IDs
    public bool $hasUnsavedChanges = false;

    public function mount(): void
    {
        $this->patientResults = collect();
        $this->medicationOptions = Medication::orderBy('name')->get();
        $this->labTestOptions = LabTestDefinition::orderBy('test_name')->get();
        $this->labTechnicianOptions = User::where('role', 'lab-technician')->orderBy('name')->get();
    }

    // ... [Search Logic remains the same] ...

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
            ->where(function ($patientQuery) use ($terms) {
                foreach ($terms as $term) {
                    $patientQuery->orWhere('first_name', 'like', "%$term%")
                        ->orWhere('last_name', 'like', "%$term%")
                        ->orWhere('patient_uid', 'like', "%$term%");
                }
            })
            ->limit(10)
            ->get();
    }

    public function selectPatient(int $id): void
    {
        $this->selectedPatientId = $id;
        $this->patientQuery = '';
        $this->patientResults = collect();
        $this->updatedSelectedPatientId();
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
        $this->attachments = [];
    }

    // ... [Prescription & Lab Logic remains mostly the same, ensuring array keys are preserved] ...

    public function addMedication()
    {
        if (empty($this->selectedMedicationId)) return;
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
            $this->selectedMedicationId = '';
        }
    }

    public function removeMedication($index)
    {
        unset($this->prescriptionItems[$index]);
        $this->prescriptionItems = array_values($this->prescriptionItems);
        $this->hasUnsavedChanges = true;
    }

    public function addLabTest()
    {
        if (empty($this->selectedLabTestId)) return;
        $lab = $this->labTestOptions->firstWhere('id', $this->selectedLabTestId);
        if ($lab) {
            $this->labItems[] = [
                'lab_test_definition_id' => $lab->id,
                'test_name' => $lab->test_name,
                'urgency' => 'normal',
                'lab_tech_id' => '',
                'reason' => '',
            ];
            $this->hasUnsavedChanges = true;
            $this->selectedLabTestId = '';
        }
    }

    public function removeLabTest($index)
    {
        unset($this->labItems[$index]);
        $this->labItems = array_values($this->labItems);
        $this->hasUnsavedChanges = true;
    }

    // --- UPDATED FILE LOGIC ---

    // 1. Handle live validation for multiple files
    public function updatedAttachments(): void
    {
        $this->validateOnly('attachments.*'); // Validate specific array items
        $this->hasUnsavedChanges = true;
    }

    // 2. Remove a temporary file before upload
    public function removeTempAttachment($index)
    {
        array_splice($this->attachments, $index, 1);
    }

    // 3. Save Multiple Files
    protected function saveAttachments(MedicalRecordModel $record): void
    {
        if (empty($this->attachments)) {
            return;
        }

        foreach ($this->attachments as $file) {
            // S3 Store
            $path = $file->store('medical_records/' . $record->id, 's3');

            MedicalRecordAttachment::create([
                'medical_record_id' => $record->id,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getClientMimeType(),
            ]);
        }

        // Clear temp array
        $this->attachments = [];
        $this->loadStoredAttachments();
    }

    // 4. Load & Remove Stored Attachments
    protected function loadStoredAttachments(): void
    {
        if (!$this->medicalRecord) {
            $this->storedAttachments = [];
            return;
        }

        $this->storedAttachments = $this->medicalRecord->attachments()->get()->all();
        // Pre-sign URLs
        $this->attachmentUrls = [];
        foreach($this->storedAttachments as $att) {
            $this->attachmentUrls[$att->id] = Storage::disk('s3')->temporaryUrl($att->file_path, now()->addMinutes(20));
        }
    }

    public function removeStoredAttachment(int $id): void
    {
        $att = MedicalRecordAttachment::find($id);
        if ($att && $att->medical_record_id === $this->medicalRecord->id) {
            // Optional: Delete from S3 immediately or via Observer
            // Storage::disk('s3')->delete($att->file_path);
            $att->delete();
            $this->loadStoredAttachments();
        }
    }

    // ... [Save Logic (saveAll, loadDrafts) remains same, ensure saveAttachments() is called] ...

    public function saveDraft(): void { $this->saveAll(false); }
    public function saveAndSign(): void { $this->saveAll(true); }

    public function saveAll(bool $finalize = false): void
    {
        $this->validate();

        DB::beginTransaction();
        try {
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

            // ... [Prescription / Lab saving logic same as original] ...

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
                    'lab_tech_id' => $lab['lab_tech_id'],
                    'reason_for_test' => $lab['reason'],
                    'request_date' => now(),
                    'status' => 'requested',
                ]);
            }

            // Fix: Pass record
            $this->saveAttachments($record);

            DB::commit();

            if ($finalize) {
                $this->resetContext();
                $this->selectedPatientId = null;
                LivewireAlert::title('Signed')->text('Consultation finalized.')->success()->show();
            } else {
                $this->updatedSelectedPatientId(); // Reload draft state
                LivewireAlert::title('Saved')->text('Draft saved.')->success()->show();
            }

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Save failed: '.$e->getMessage());
            LivewireAlert::error('Failed to save record.')->show();
        }
    }

    // ... [Load Logic remains same] ...
    protected function loadDraftPrescriptions()
    {
        if (!$this->medicalRecord) return;
        $prescription = Prescription::with('items.medication')->where('consultation_id', $this->medicalRecord->id)->first();
        if ($prescription) {
            $this->prescriptionItems = [];
            foreach ($prescription->items as $item) {
                $this->prescriptionItems[] = [
                    'medication_id' => $item->medication_id,
                    'name' => $item->medication->name ?? 'Unknown',
                    'dosage' => $item->dosage,
                    'frequency' => $item->frequency,
                    'duration' => $item->duration,
                ];
            }
        }
    }

    protected function loadDraftLabs()
    {
        if (!$this->medicalRecord) return;
        $labs = LabRequest::with('testDefinition')->where('consultation_id', $this->medicalRecord->id)->get();
        $this->labItems = [];
        foreach ($labs as $lab) {
            $this->labItems[] = [
                'lab_test_definition_id' => $lab->lab_test_definition_id,
                'test_name' => $lab->testDefinition->test_name ?? 'Unknown',
                'urgency' => $lab->urgency_level,
                'lab_tech_id' => $lab->lab_tech_id,
                'reason' => $lab->reason_for_test,
            ];
        }
    }

    public function render()
    {
        return view('livewire.tenants.doctor.medical-record');
    }
}
