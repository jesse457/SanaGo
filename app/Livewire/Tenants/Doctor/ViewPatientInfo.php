<?php

namespace App\Livewire\Tenants\Doctor;

use App\Models\Admission;
use App\Models\Patient as PatientModel;
use App\Services\MedicalRecordService;
use App\Traits\UserActivitiesTrait;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.doctor')]
class ViewPatientInfo extends Component
{
    use UserActivitiesTrait;

    public PatientModel $patient;

    public ?Admission $admission;

    public function mount(PatientModel $patient, MedicalRecordService $service)
    {
        // Use service to load all data consistently with the API
        $this->patient = $service->getPatientProfile($patient);
        $this->admission = $this->patient->admissions->first();
    }

    public function requestPatientAdmit(MedicalRecordService $service)
    {
        try {
            $this->admission = $service->requestAdmission($this->patient, Auth::user());
            $this->patient->refresh();
            LivewireAlert::title('Admission Confirmed!')
                ->success()
                ->text('Admission request sent successfully.')
                ->show();

        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage());
        }
    }

    public function previewAttachment($attachmentId, MedicalRecordService $service)
    {
        $preview = $service->getAttachmentPreview($attachmentId);
        // Dispatch to browser to open URL in new tab
        $this->dispatch('open-url', url: $preview['url']);
    }
}
