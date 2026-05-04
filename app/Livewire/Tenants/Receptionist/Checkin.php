<?php

declare(strict_types=1);

namespace App\Livewire\Tenants\Receptionist;

use App\Models\Patient;
use App\Services\AdmissionService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.receptionist')]
class Checkin extends Component
{
    use WithPagination;

    public string $search = '';

    public int $perPage = 10;

    // Modal State
    public bool $showAdmissionModal = false;

    public ?int $selectedPatientId = null;

    public Collection $recentAdmissions;

    public ?int $selectedAdmissionId = null;

    public function mount()
    {
        $this->recentAdmissions = collect();
    }

    public function admitPatient(int $patientId, AdmissionService $service): void
    {
        $patient = Patient::find($patientId);

        if (! $patient) {
            $this->alert('warning', 'Patient Not Found');

            return;
        }

        try {
            // 1. Fetch History using new Service method
            $this->recentAdmissions = $service->getAdmissionsForPatient($patient);

            // 2. Auto-select logic
            $latest = $this->recentAdmissions->first();

            // If the patient is already currently admitted, prevent action
            if ($latest && $latest->status === 'Admitted') {
                $this->alert('info', 'Patient is already admitted.', [
                    'text' => 'Bed: '.($latest->bed?->code ?? 'Unassigned'),
                ]);

                return;
            }

            // If there is a pending request, select it by default
            $this->selectedAdmissionId = ($latest && $latest->status === 'Pending')
                ? $latest->id
                : null;

            $this->selectedPatientId = $patientId;
            $this->showAdmissionModal = true;

        } catch (\RuntimeException $e) {
            $this->alert('warning', 'Access Error', ['text' => $e->getMessage()]);
        }
    }

    public function confirmAdmission(bool $redirectToForm): void
    {
        if (! $this->selectedPatientId) {
            $this->closeModal();

            return;
        }

        // Logic Refactored:
        // The new Service's confirmAdmission() requires Bed ID, Date, and Reason.
        // Therefore, we cannot do a "Quick Admit" (one-click) from this list view anymore.
        // We must redirect to the full Admit Patient form to gather that data.

        $routeParams = $this->selectedAdmissionId
            ? ['admission' => $this->selectedAdmissionId]
            : ['patient' => $this->selectedPatientId];

        $this->closeModal();
        $this->redirect(route('receptionist.admit-patient', $routeParams), true);
    }

    public function render(AdmissionService $service)
    {
        $patients = $service->getPatientsForCheckin($this->search, $this->perPage);

        return view('livewire.tenants.receptionist.checkin', [
            'patients' => $patients,
            'userRole' => Auth::user()->role ?? 'receptionist',
        ]);
    }

    public function closeModal(): void
    {
        $this->reset(['showAdmissionModal', 'selectedPatientId', 'selectedAdmissionId']);
        $this->recentAdmissions = collect();
    }
}
