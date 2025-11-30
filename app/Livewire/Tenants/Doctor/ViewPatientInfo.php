<?php

namespace App\Livewire\Tenants\Doctor;

use App\Models\Admission;
use App\Models\Patient as PatientModel;
use App\Models\Attachment;
use App\Models\MedicalRecordAttachment;
use App\Models\MedicalRecord;
use App\Traits\UserActivitiesTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\Rule;

#[Layout('components.layouts.doctor')]
class ViewPatientInfo extends Component
{
    use UserActivitiesTrait;

    public PatientModel $patient;
    public ?Admission $admission;

    public bool $showLabModal = false;
    public bool $showPrescriptionModal = false;
    public bool $showEditConsultationModal = false;
    public int $activeRecordId = 0;

    // Edit consultation form properties
    public ?MedicalRecord $editingRecord = null;
    public string $diagnosis_text = '';
    public string $treatment_plan = '';
    public string $complaint = '';
    public string $general_notes = '';

    // Attachment preview state
    public ?string $attachmentPreviewUrl = null;
    public ?string $attachmentPreviewMime = null;
    public bool $showAttachmentPreview = false;
    public int $previewingAttachmentId = 0;

    // Loading states
    public bool $isUpdatingConsultation = false;
    public bool $isRequestingAdmission = false;

    protected $listeners = [
        'open-lab-request-modal' => 'openLabModal',
        'open-prescription-modal' => 'openPrescriptionModal',
    ];

    public function openLabModal($recordId)
    {
        $this->activeRecordId = $recordId;
        $this->showLabModal = true;
    }

    public function openPrescriptionModal($recordId)
    {
        $this->activeRecordId = $recordId;
        $this->showPrescriptionModal = true;
    }

    public function openEditConsultationModal($recordId)
    {
        $this->editingRecord = MedicalRecord::findOrFail($recordId);

        // Check if the current user is authorized to edit this record
        if ($this->editingRecord->doctor_id !== Auth::id()) {
            throw new AuthorizationException('You are not authorized to edit this consultation.');
        }

        // Load the current values
        $this->diagnosis_text = $this->editingRecord->diagnosis_text ?? '';
        $this->treatment_plan = $this->editingRecord->treatment_plan ?? '';
        $this->complaint = $this->editingRecord->complaint ?? '';
        $this->general_notes = $this->editingRecord->general_notes ?? $this->editingRecord->soap_notes ?? '';

        $this->showEditConsultationModal = true;
    }

    public function closeEditConsultationModal()
    {
        $this->showEditConsultationModal = false;
        $this->reset(['editingRecord', 'diagnosis_text', 'treatment_plan', 'complaint', 'general_notes']);
        $this->resetErrorBag();
    }

    public function updateConsultation()
    {
        $this->isUpdatingConsultation = true;

        $this->validate([
            'diagnosis_text' => 'required|string',
            'treatment_plan' => 'nullable|string',
            'complaint' => 'nullable|string',
            'general_notes' => 'nullable|string',
        ]);

        if (!$this->editingRecord) {
            $this->isUpdatingConsultation = false;
            return;
        }

        // Store old values for activity log
        $oldValues = [
            'diagnosis_text' => $this->editingRecord->diagnosis_text,
            'treatment_plan' => $this->editingRecord->treatment_plan,
            'complaint' => $this->editingRecord->complaint,
            'general_notes' => $this->editingRecord->general_notes ?? $this->editingRecord->soap_notes,
        ];

        // Update the record
        $this->editingRecord->update([
            'diagnosis_text' => $this->diagnosis_text,
            'treatment_plan' => $this->treatment_plan,
            'complaint' => $this->complaint,
            'general_notes' => $this->general_notes,
        ]);

        // Log the activity
        $doctorName = Auth::user()->name ?? 'Unknown Doctor';
        $patientName = $this->patient->first_name ?? $this->patient->name ?? null;

        $this->logActivity(
            "Consultation_Updated",
            "Doctor {$doctorName} (ID: ".Auth::id().") updated consultation for Patient {$patientName} (ID: {$this->patient->id})",
            [
                'target' => 'MedicalRecord',
                'patient_id' => $this->patient->id,
                'record_id' => $this->editingRecord->id,
                'old_values' => $oldValues,
                'new_values' => [
                    'diagnosis_text' => $this->diagnosis_text,
                    'treatment_plan' => $this->treatment_plan,
                    'complaint' => $this->complaint,
                    'general_notes' => $this->general_notes,
                ]
            ]
        );

        // Refresh the patient data to show updated information
        $this->patient->load([
            'medicalRecords.doctor',
            'medicalRecords.attachments',
        ]);

        $this->closeEditConsultationModal();
        $this->isUpdatingConsultation = false;

        LivewireAlert::title('Consultation Updated')
            ->success()
            ->text('The consultation details have been updated successfully.')
            ->show();
    }

    public function mount(PatientModel $patient)
    {
        $this->patient = $patient->load([
            'medicalRecords.doctor',
            'medicalRecords.attachments',
        ]);

        $this->admission = $this->patient->admissions()->latest('created_at')->first();
    }

    public function requestPatientAdmit($userId)
    {
        $this->isRequestingAdmission = true;

        $this->patient->update(['is_admitted_approve' => true]);

        $admission = new Admission;
        $admission->patient_id = $userId;
        $admission->doctor_id = Auth::id();
        $admission->status = 'Pending';
        $admission->save();

        $doctorName = Auth::user()->name ?? 'Unknown Doctor';
        $patientName = $this->patient->first_name ?? $this->patient->name ?? null;

        $this->logActivity(
            "Request_for_admission",
            "Doctor {$doctorName} (ID: ".Auth::id().") requested admission for Patient {$patientName} (ID: {$userId})",
            [
                'target' => 'Patient',
                'patient_id' => $userId,
                'admission_id' => $admission->id,
            ]
        );

        $this->admission = $admission;
        $this->isRequestingAdmission = false;

        LivewireAlert::title('Success')
            ->success()
            ->text('Patient admission request sent successfully.')
            ->show();
    }

    /**
     * Open attachment preview modal for a given attachment id.
     */
    public function previewAttachment(int $attachmentId): void
    {
        $attachment = MedicalRecordAttachment::findOrFail($attachmentId);

        // Always use the secure route for local/private disk
         $this->attachmentPreviewUrl = Storage::disk('s3')->temporaryUrl($attachment->file_path,now()->addMinutes(5));

        // Mime type best-effort
        try {
            $this->attachmentPreviewMime = Storage::disk('s3')
                ->mimeType($attachment->file_path);
        } catch (\Throwable $e) {
            $this->attachmentPreviewMime = null;
        }

        $this->showAttachmentPreview = true;
        $this->previewingAttachmentId = $attachment->id;
    }

    public function closeAttachmentPreview(): void
    {
        $this->showAttachmentPreview = false;
        $this->attachmentPreviewUrl = null;
        $this->attachmentPreviewMime = null;
        $this->previewingAttachmentId = 0;
    }

    public function render()
    {
        return view('livewire.tenants.doctor.view-patient-info');
    }
}
