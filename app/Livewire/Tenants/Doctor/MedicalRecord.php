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
use App\Notifications\NewLabOrderNotification;
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

    // Search & Selection
    #[Rule('required|exists:patients,id')]
    public ?int $selectedPatientId = null;
    public string $patientQuery = '';
    public Collection $patientResults;

    // Clinical Data
    #[Rule('required|string|max:65535')]
    public ?string $complaint = null;

    #[Rule('nullable|string|max:65535')]
    public ?string $diagnosisText = null;

    #[Rule('nullable|string|max:65535')]
    public ?string $clinicalNotes = null;

    // Prescriptions (Dropdown Data)
    public Collection $allMedications;
    public array $prescriptionItems = [];

    // Lab Requests (Dropdown Data)
    public Collection $allLabTests;
    public array $labItems = [];
    public Collection $labTechnicianOptions;

    // Attachments
    #[Rule(['attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,pdf'])]
    public $attachments = [];
    public array $storedAttachments = [];
    public array $attachmentUrls = [];
    public bool $hasUnsavedChanges = false;

    public function mount(): void
    {
        $this->patientResults = collect();

        // LOAD DROPDOWN DATA
        // Select specific columns to reduce memory usage
        $this->allMedications = Medication::select('id', 'name', 'stock_quantity')
            ->orderBy('name')
            ->get();

        $this->allLabTests = LabTestDefinition::select('id', 'test_name', 'code')
            ->orderBy('test_name')
            ->get();

        $this->labTechnicianOptions = User::where('role', 'lab-technician')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    // --- Search Logic (Patient Only) ---

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
                } catch (\Throwable $e) {
                    // Fallback or log if blind index fails
                }
            })
            ->limit(10)
            ->get();
    }

    // --- Patient Selection ---

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
            $this->resetContext();
            return;
        }

        // Find existing Draft
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

    // --- Prescriptions Logic ---

    // Updated to accept ID from View
    public function addMedication($medicationId)
    {
        if (empty($medicationId)) return;

        // Fetch from collection or DB
        $med = $this->allMedications->where('id', $medicationId)->first();
        // Fallback to DB if for some reason not in collection (rare)
        if (!$med) $med = Medication::find($medicationId);

        if ($med) {
            // Check for duplicates
            foreach ($this->prescriptionItems as $item) {
                if ($item['medication_id'] == $med->id) return;
            }

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

    // --- Lab Logic ---

    // Updated to accept ID from View
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

    // --- File Handling ---

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
        $att = MedicalRecordAttachment::where('id', $id)
            ->where('medical_record_id', $this->medicalRecord?->id)
            ->first();

        if ($att) {
            try {
                Storage::disk('s3')->delete($att->file_path);
            } catch (\Exception $e) {
                // Log but continue deletion of record
                Log::error("S3 Delete failed: " . $e->getMessage());
            }
            $att->delete();
            $this->loadStoredAttachments();
        }
    }

    protected function loadStoredAttachments(): void
    {
        if (!$this->medicalRecord) {
            $this->storedAttachments = [];
            return;
        }

        $this->storedAttachments = $this->medicalRecord->attachments()->get()->all();
        $this->attachmentUrls = [];

        foreach ($this->storedAttachments as $att) {
            try {
                $this->attachmentUrls[$att->id] = Storage::disk('s3')->temporaryUrl($att->file_path, now()->addMinutes(30));
            } catch (\Exception $e) {
                Log::warning("Could not generate URL for attachment {$att->id}: " . $e->getMessage());
                $this->attachmentUrls[$att->id] = '#';
            }
        }
    }

    // --- Save Logic ---

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

        if (!$this->selectedPatientId) return;

        try {
            DB::transaction(function () use ($finalize) {

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
                    $this->medicalRecord = $record;
                }

                // 2. Save Attachments
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

                // 3. Save Prescriptions
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
                    }

                    $prescription->items()->delete();

                    $prescItemsData = [];
                    foreach ($this->prescriptionItems as $item) {
                        $prescItemsData[] = [
                            'prescription_id' => $prescription->id,
                            'medication_id' => $item['medication_id'],
                            'dosage' => $item['dosage'],
                            'frequency' => $item['frequency'],
                            'duration' => $item['duration'],
                            'quantity_prescribed' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    if (!empty($prescItemsData)) {
                        PrescriptionItem::insert($prescItemsData);
                    }
                } else {
                    Prescription::where('consultation_id', $record->id)
                        ->where('status', 'draft')
                        ->delete();
                }

                // 4. Save Lab Requests
                $currentRequestIds = [];
                $notificationsToSend = [];

                foreach ($this->labItems as $lab) {
                    $labReq = LabRequest::updateOrCreate(
                        [
                            'consultation_id' => $record->id,
                            'lab_test_definition_id' => $lab['lab_test_definition_id'],
                        ],
                        [
                            'patient_id' => $this->selectedPatientId,
                            'requested_by_doctor_id' => Auth::id(),
                            'urgency_level' => $lab['urgency'],
                            'lab_tech_id' => $lab['lab_tech_id'] ?: null,
                            'reason_for_test' => $lab['reason'],
                            'request_date' => now(),
                        ]
                    );

                    if ($labReq->wasRecentlyCreated) {
                        $labReq->status = 'requested';
                        $labReq->save();
                    }

                    $currentRequestIds[] = $labReq->id;

                    if ($lab['lab_tech_id'] && $labReq->wasRecentlyCreated) {
                        $notificationsToSend[] = ['tech_id' => $lab['lab_tech_id'], 'model' => $labReq];
                    }
                }

                // Cleanup removed labs
                LabRequest::where('consultation_id', $record->id)
                    ->whereNotIn('id', $currentRequestIds)
                    ->whereIn('status', ['requested', 'draft'])
                    ->delete();

                // Send Notifications
                foreach ($notificationsToSend as $notif) {
                    $tech = User::find($notif['tech_id']);
                    if ($tech) {
                        $tech->notify(new NewLabOrderNotification($notif['model']));
                    }
                }
            });

            $this->loadStoredAttachments();

            if ($finalize) {
                $this->resetContext();
                LivewireAlert::title('Signed')->text('Consultation finalized.')->success()->show();
            } else {
                $this->updatedSelectedPatientId();
                LivewireAlert::title('Saved')->text('Draft saved.')->success()->show();
            }
        } catch (\Throwable $e) {
            Log::error('Medical Record Save Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            LivewireAlert::error('Failed to save record. Please try again.')->show();
        }
    }

    // --- Loading Drafts ---

    protected function loadDraftPrescriptions()
    {
        if (!$this->medicalRecord) return;

        $prescription = Prescription::with('items.medication')
            ->where('consultation_id', $this->medicalRecord->id)
            ->first();

        $this->prescriptionItems = [];
        if ($prescription) {
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

        $labs = LabRequest::with('testDefinition')
            ->where('consultation_id', $this->medicalRecord->id)
            ->get();

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
