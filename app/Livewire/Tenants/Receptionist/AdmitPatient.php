<?php

namespace App\Livewire\Tenants\Receptionist;

use App\Models\Admission;
use App\Services\AdmissionService;
use Illuminate\Support\Collection;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.receptionist')]
class AdmitPatient extends Component
{
    public Admission $admission;

    // --- Livewire Properties (camelCase) ---

    #[Validate('required|exists:beds,id')]
    public ?int $bedId = null;

    #[Validate('required|string|max:255')]
    public ?string $reasonForAdmission = '';

    #[Validate('required|date')]
    public string $admissionDate = '';

    #[Validate('required|integer|min:0')]
    public int $observationFee = 0;

    public Collection $availableBeds;

    protected AdmissionService $admissionService;

    public function boot(AdmissionService $admissionService)
    {
        $this->admissionService = $admissionService;
    }

    public function mount(Admission $admission): void
    {
        $this->admission = $admission;

        // Populate Livewire properties from DB model
        $this->reasonForAdmission = $admission->reason_for_admission;
        $this->bedId = $admission->bed_id;
        $this->admissionDate = now()->format('Y-m-d');

        $this->availableBeds = $this->admissionService->getAvailableBeds();
    }

    public function saveAdmission(): void
    {
        // 1. Validate Livewire properties (returns camelCase keys)
        $validated = $this->validate();

        // 2. Map camelCase to snake_case for the Service
        $serviceData = [
            'bed_id' => $validated['bedId'],
            'reason_for_admission' => $validated['reasonForAdmission'],
            'admission_date' => $validated['admissionDate'],
            'observation_fee' => $validated['observationFee'],
        ];

        try {
            // 3. Call Service with mapped data
            $this->admissionService->confirmAdmission($this->admission, $serviceData);

            LivewireAlert::title('Admission Confirmed!')
                ->success()
                ->text("Patient {$this->admission->patient->full_name} has been successfully admitted.")
                ->show();

            $this->js('setTimeout(() => window.location.href = "'.route('receptionist.checkin').'", 2500)');

        } catch (\Exception $e) {
            LivewireAlert::title('Error')
                ->error()
                ->text($e->getMessage())
                ->show();
        }
    }

    public function render()
    {
        return view('livewire.tenants.receptionist.admit-patient');
    }
}
