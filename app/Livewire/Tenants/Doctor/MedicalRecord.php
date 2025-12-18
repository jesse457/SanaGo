<?php

namespace App\Livewire\Tenants\Doctor;

// --- MODEL IMPORTS ---
use App\Models\LabRequest;
use App\Models\LabTestDefinition;
use App\Models\MedicalRecord as MedicalRecordModel;
use App\Models\MedicalRecordAttachment;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\User;

// --- EVENT IMPORTS (EFFICIENCY UPGRADE) ---
use App\Events\NewPrescriptionEvent;
use App\Events\NewLabRequestEvent;

// --- UTILITY IMPORTS ---
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

    // --- STATE PROPERTIES ---
    public ?Patient $patient = null;
    public ?MedicalRecordModel $medicalRecord = null;

    // --- SEARCH & SELECTION ---
    #[Rule('required|exists:patients,id')]
    public ?int $selectedPatientId = null;
    public string $patientQuery = '';
    public Collection $patientResults;

    // --- CLINICAL DATA ---
    #[Rule('required|string|max:65535')]
    public ?string $complaint = null;

    #[Rule('nullable|string|max:65535')]
    public ?string $diagnosisText = null;

    #[Rule('nullable|string|max:65535')]
    public ?string $clinicalNotes = null;

    // --- MEDICATION DATA ---
    public Collection $allMedications;
    public array $prescriptionItems = [];

    // --- LAB DATA ---
    public Collection $allLabTests;
    public array $labItems = [];
    public Collection $labTechnicianOptions;

    // --- ATTACHMENTS ---
    #[Rule(['attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,pdf'])]
    public $attachments = [];
    public array $storedAttachments = [];
    public array $attachmentUrls = [];

    public bool $hasUnsavedChanges = false;

    // --- MOUNT ---
    public function mount(): void
    {
        $this->patientResults = collect();

        // Optimized queries for dropdowns
        $this->allMedications = Medication::select('id', 'name', 'stock_quantity')->orderBy('name')->get();
        $this->allLabTests = LabTestDefinition::select('id', 'test_name', 'code')->orderBy('test_name')->get();
        $this->labTechnicianOptions = User::where('role', 'lab-technician')->select('id', 'name')->orderBy('name')->get();
    }

    // --- PATIENT SEARCH ---
    public function updatedPatientQuery(): void
    {
        $this->validate(['patientQuery' => 'nullable|string|min:2']);
        $q = trim($this->patientQuery);

        if ($q === '') {
            $this->patientResults = collect();
            return;
        }

        $terms = explode(' ', $this->patientQuery);
        $this->patientResults = Patient::query()
            ->where(function ($q) use ($terms) {
                try {
                    // Blind Index Search Logic
                    if (count($terms) === 2) {
                        $q->WhereBlind('first_name', 'first_name_index', $terms[0]);
                        $q->WhereBlind('last_name', 'last_name_index', $terms[1]);
                    } else {
                        foreach ($terms as $term) {
                            $q->orWhereBlind('first_name', 'first_name_index', $term)
                                ->orWhereBlind('last_name', 'last_name_index', $term)
                                ->orWhere('patient_uid', 'like', "%$term%");
                        }
                    }
                } catch (\Throwable $e) { }
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

    // --- LOAD PATIENT DATA & DRAFT ---
    public function updatedSelectedPatientId(): void
    {
        if (! $this->selectedPatientId) {
            $this->resetContext();
            return;
        }

        $this->patient = Patient::find($this->selectedPatientId);

        if (! $this->patient) {
            $this->resetContext();
            return;
        }

        // Check for existing Draft
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
            $this->resetDraftFields();
        }

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
        $this->storedAttachments = [];
        $this->attachmentUrls = [];
        $this->attachments = [];
    }

    // --- UI HELPERS (ADD/REMOVE ITEMS) ---

    public function addMedication($medicationId)
    {
        if (empty($medicationId)) return;
        $med = $this->allMedications->where('id', $medicationId)->first();
        if (!$med) $med = Medication::find($medicationId);

        if ($med) {
            foreach ($this->prescriptionItems as $item) {
                if ($item['medication_id'] == $med->id) return;
            }
            $this->prescriptionItems[] = [
                'medication_id' => $med->id,
                'name' => $med->name,
                'dosage' => '', 'frequency' => '', 'duration' => '',
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
        if (empty($testId)) return;
        $lab = $this->allLabTests->where('id', $testId)->first();
        if (!$lab) $lab = LabTestDefinition::find($testId);

        if ($lab) {
            foreach ($this->labItems as $item) {
                if ($item['lab_test_definition_id'] == $lab->id) return;
            }
            $this->labItems[] = [
                'lab_test_definition_id' => $lab->id,
                'test_name' => $lab->test_name,
                'urgency' => 'normal',
                'lab_tech_id' => '',
                'reason' => '',
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

    // --- FILE UPLOADS ---

    public function updatedAttachments(): void
    {
        $this->validateOnly('attachments.*');
        $this->hasUnsavedChanges = true;
    }

    public function removeTempAttachment($index)
    {
        array_splice($this->attachments, $index, 1);
    }

    public function removeStoredAttachment(int $id): void
    {
        $att = MedicalRecordAttachment::where('id', $id)->first();
        if ($att) {
            try { Storage::disk('s3')->delete($att->file_path); } catch (\Exception $e) {}
            $att->delete();
            $this->loadStoredAttachments();
        }
    }

    protected function loadStoredAttachments(): void
    {
        if (!$this->medicalRecord) {
            $this->storedAttachments = []; return;
        }
        $this->storedAttachments = $this->medicalRecord->attachments()->get()->all();
        $this->attachmentUrls = [];
        foreach ($this->storedAttachments as $att) {
            try { $this->attachmentUrls[$att->id] = Storage::disk('s3')->temporaryUrl($att->file_path, now()->addMinutes(30)); }
            catch (\Exception $e) { $this->attachmentUrls[$att->id] = '#'; }
        }
    }

    // --- SAVE LOGIC ---

    public function saveDraft(): void { $this->saveAll(false); }
    public function saveAndSign(): void { $this->saveAll(true); }

    public function saveAll(bool $finalize = false): void
    {
        $this->validate();
        if (!$this->selectedPatientId) return;

        try {
            // We use DB transaction to ensure data integrity
            DB::transaction(function () use ($finalize) {

                // 1. SAVE MEDICAL RECORD
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
                    $this->medicalRecord = $record;
                }

                // 2. ATTACHMENTS
                if (!empty($this->attachments)) {
                    foreach ($this->attachments as $file) {
                        $path = $file->store('medical_records/' . $record->id, 's3');
                        MedicalRecordAttachment::create([
                            'medical_record_id' => $record->id,
                            'file_path' => $path,
                            'file_name' => $file->getClientOriginalName(),
                            'file_type' => $file->getClientMimeType(),
                        ]);
                    }
                    $this->attachments = [];
                }

                // 3. PRESCRIPTIONS (SYNC)
                $prescriptionEventToFire = null;

                if (count($this->prescriptionItems) > 0) {
                    $prescription = Prescription::firstOrCreate(
                        ['consultation_id' => $record->id],
                        [
                            'patient_id' => $this->selectedPatientId,
                            'doctor_id' => Auth::id(),
                            'prescription_date' => now(),
                            'status' => $finalize ? 'prescribed' : 'draft',
                        ]
                    );

                    if ($finalize) {
                        $prescription->update(['status' => 'prescribed']);
                        $prescriptionEventToFire = $prescription; // Prepare event data
                    }

                    $prescription->items()->delete(); // Sync strategy

                    $prescItemsData = [];
                    foreach ($this->prescriptionItems as $item) {
                        $prescItemsData[] = [
                            'tenant_id' => $prescription->tenant_id,
                            'prescription_id' => $prescription->id,
                            'medication_id' => $item['medication_id'],
                            'dosage' => $item['dosage'], 'frequency' => $item['frequency'], 'duration' => $item['duration'],
                            'quantity_prescribed' => 1, 'created_at' => now(), 'updated_at' => now(),
                        ];
                    }
                    if (!empty($prescItemsData)) PrescriptionItem::insert($prescItemsData);

                } else {
                    // Empty list -> delete draft prescription
                    Prescription::where('consultation_id', $record->id)->where('status', 'draft')->delete();
                }

                // 4. LAB REQUESTS (SYNC)
                $currentRequestIds = [];
                $labOrdersCreated = false;

                foreach ($this->labItems as $lab) {
                    $labReq = LabRequest::updateOrCreate(
                        ['consultation_id' => $record->id, 'lab_test_definition_id' => $lab['lab_test_definition_id']],
                        [
                            'patient_id' => $this->selectedPatientId,
                             'tenant_id' => $prescription->tenant_id,
                            'requested_by_doctor_id' => Auth::id(),
                            'urgency_level' => $lab['urgency'],
                            // 'lab_tech_id' => $lab['lab_tech_id'], // Optional: specific assignment
                            'reason_for_test' => $lab['reason'],
                            'request_date' => now(),
                        ]
                    );

                    if ($labReq->wasRecentlyCreated) {
                        $labReq->status = 'requested';
                        $labReq->save();
                        $labOrdersCreated = true;
                    }
                    $currentRequestIds[] = $labReq->id;
                }

              

                // 5. EVENT BROADCASTING (HIGH EFFICIENCY)

                // A. Broadcast to Pharmacy Department
                if ($prescriptionEventToFire) {
                    // This creates 1 Broadcast Event for ALL pharmacists
                    event(new NewPrescriptionEvent($prescriptionEventToFire));
                }

                // B. Broadcast to Lab Department
                if ($labOrdersCreated) {
                    // This creates 1 Broadcast Event for ALL lab technicians
                    event(new NewLabRequestEvent($record));
                }

            }); // End Transaction

            $this->loadStoredAttachments();

            if ($finalize) {
                $this->resetContext();
                LivewireAlert::title('Signed')->text('Consultation finalized & orders sent.')->success()->show();
            } else {
                $this->updatedSelectedPatientId();
                LivewireAlert::title('Saved')->text('Draft saved.')->success()->show();
            }
        } catch (\Throwable $e) {
            Log::error('Medical Record Save Error: ' . $e->getMessage());
            LivewireAlert::error('Failed to save record.')->show();
        }
    }

    // --- DB LOADERS ---

    protected function loadDraftPrescriptions()
    {
        if (!$this->medicalRecord) return;
        $prescription = Prescription::with('items.medication')->where('consultation_id', $this->medicalRecord->id)->first();
        $this->prescriptionItems = [];
        if ($prescription) {
            foreach ($prescription->items as $item) {
                $this->prescriptionItems[] = [

                    'medication_id' => $item->medication_id,
                    'name' => $item->medication->name ?? 'Unknown',
                    'dosage' => $item->dosage, 'frequency' => $item->frequency, 'duration' => $item->duration,
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
