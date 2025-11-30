<?php

declare(strict_types=1);

namespace App\Livewire\Tenants\Receptionist;

use App\Models\Admission;
use App\Models\Bed;
use App\Models\Patient;
use App\Traits\UserActivitiesTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.receptionist')]
class ViewAdmissionDetails extends Component
{
    use UserActivitiesTrait,WithPagination;

    public Patient $patient;

    /**
     * @var Collection<Admission> A collection of all admissions for the patient.
     */
    public Collection $admissions;

    /**
     * @var int|null The ID of the currently selected admission.
     */
    public ?int $selectedAdmissionId = null;

    public ?Admission $selectedAdmission = null;

    public string $currentStatus = 'N/A';

    /**
     * Mount the component, fetching patient and all of their admissions.
     *
     * @param  Patient  $patient  The patient model instance via route model binding.
     */
    public function mount(Patient $patient): void
    {
        $this->patient = $patient->load(['admissions' => function ($query) {
            $query->with(['doctor', 'bed.ward']);
        }]);

        // Sort admissions by the most recent first
        $this->admissions = $this->patient->admissions->sortByDesc('admission_date');

        // Set the latest admission as the default selected one
        if ($this->admissions->isNotEmpty()) {
            $latestAdmission = $this->admissions->first();
            $this->selectedAdmissionId = $latestAdmission->id;
            $this->loadAdmissionDetails();
        }
    }

    /**
     * Livewire lifecycle hook that is called when a property is updated.
     * We'll use this to load admission details when the user changes the dropdown selection.
     */
    public function updatedSelectedAdmissionId(): void
    {
        $this->loadAdmissionDetails();
    }

    /**
     * Load the details for the currently selected admission.
     */
    public function loadAdmissionDetails(): void
    {
        if ($this->selectedAdmissionId) {
            $this->selectedAdmission = $this->admissions->firstWhere('id', $this->selectedAdmissionId);
            if ($this->selectedAdmission) {
                $this->currentStatus = $this->selectedAdmission->status;
            } else {
                $this->selectedAdmission = null;
                $this->currentStatus = 'N/A';
            }
        }
    }

    /**
     * Update the admission status for a specific admission.
     *
     * @param  int  $admissionId  The ID of the admission to update.
     * @param  string  $newStatus  The new status to set ('Discharged').
     */
    public function updateAdmissionStatus(int $admissionId, string $newStatus): void
    {
        $admissionToUpdate = $this->admissions->firstWhere('id', $admissionId);

        if (! $admissionToUpdate) {
            LivewireAlert::title('Error')->error()->text('No active admission record found to update.')->show();

            return;
        }

        // Basic validation for allowed status changes
        if ($newStatus !== 'Discharged') {
            LivewireAlert::title('Error')->error()->text('Invalid status update requested.')->show();

            return;
        }

        if ($admissionToUpdate->status === $newStatus) {
            LivewireAlert::title('Error')->error()->text("Patient is already {$newStatus}.")->show();

            return;
        }

        try {
            // Update the admission status
            $admissionToUpdate->update([
                'status' => $newStatus,
                'discharge_date' => now(),
            ]);

            // If discharged, free up the bed
            if ($newStatus === 'Discharged' && $admissionToUpdate->bed_id) {
                $bed = Bed::find($admissionToUpdate->bed_id);
                if ($bed) {
                    $bed->is_occupied = false;
                    $bed->save();
                }
            }
            $this->patient->update(['is_admitted_approve' => false]);
            // Reload data to reflect the change
            $this->admissions = $this->patient->admissions->sortByDesc('admission_date');
            $this->selectedAdmission = $this->admissions->firstWhere('id', $this->selectedAdmissionId);
            $this->currentStatus = $this->selectedAdmission->status;
            $this->logActivity('Patient_discharged',
                "Patient '{$this->patient->first_name} {$this->patient->last_name}' was Discharged "
            );
            LivewireAlert::title('Success')->success()->text("Patient '{$this->patient->first_name} {$this->patient->last_name}' successfully {$newStatus}.")->show();
        } catch (\Exception $e) {
            LivewireAlert::title('Success')->success()->text('Failed to update admission status if this error persist contact us')->show();
            Log::error('Failed to update admission status: '.$e->getMessage());
        }
    }

    /**
     * Render the component view.
     */
    public function render()
    {
        $userRole = Auth::user()->role ?? 'receptionist';

        return view('livewire.tenants.receptionist.view-admission-details', [
            'userRole' => $userRole,
        ]);
    }
}
