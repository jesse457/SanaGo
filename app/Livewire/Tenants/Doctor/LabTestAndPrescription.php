<?php

namespace App\Livewire\Tenants\Doctor;

use App\Models\LabResultAttachment;
use App\Models\MedicalRecord;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.doctor')]

class LabTestAndPrescription extends Component
{
    public MedicalRecord $consultation;

    // Attachment preview state
    public ?string $attachmentPreviewUrl = null;

    public ?string $attachmentPreviewMime = null;

    public bool $showAttachmentPreview = false;

    public int $previewingAttachmentId = 0;

    public function mount(int $consultationId): void
    {

        $this->consultation = MedicalRecord::with([
            'patient',
            'doctor',
            'prescription.items.medication',
            'labResults.attachments',
            'labResults.labTechnician',
        ])->findOrFail($consultationId);
    }

    /**
     * Open attachment preview modal for a given attachment id.
     *
     * This will:
     *  - authorize viewing the attachment via policy
     *  - try to generate a temporaryUrl for cloud disks (S3)
     *  - fallback to the attachments.stream route for local disks
     */
    public function previewAttachment(int $attachmentId): void
    {
        $attachment = LabResultAttachment::findOrFail($attachmentId);
        // Mime type best-effort
        try {
            $this->attachmentPreviewMime = Storage::disk('s3')
                ->mimeType($attachment->file_path);
        } catch (\Throwable $e) {
            $this->attachmentPreviewMime = null;
        }
        // Always use the secure route for local/private disk
        $this->attachmentPreviewUrl = Storage::disk('s3')->temporaryUrl($attachment->file_path, now()->addMinutes(5));

        $this->showAttachmentPreview = true;
        $this->previewingAttachmentId = $attachment->id;
    }

    public function render()
    {

        return view('livewire.tenants.doctor.lab-test-and-prescription');
    }
}
