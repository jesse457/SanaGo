<?php

namespace App\Livewire\Tenants\Receptionist;

use App\Models\Admission;
use App\Models\Bed;
use App\Models\Patient;
use App\Traits\UserActivitiesTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.receptionist')]
class AdmitPatient extends Component
{
    use UserActivitiesTrait;

    // The core model is now Admission
    public Admission $admission;

    public Patient $patient;

    #[Validate('required|exists:beds,id')]
    public ?int $bedId;

    #[Validate('required|string|max:255')]
    public ?string $reasonForAdmission;

    #[Validate('required|date')]
    public string $admissionDate;

    #[Validate('required|integer')]
    public int $observationFee;

    public Collection $availableBeds;

    /**
     * Mount the component with an Admission model.
     */
    public function mount(Admission $admission): void
    {
        // Load the admission and its associated patient
        $this->admission = $admission;
        $this->patient = $admission->patient;

        // Pre-fill form fields from the existing admission record
        $this->reasonForAdmission = $this->admission->reason_for_admission;
        $this->bedId = $this->admission->bed_id; // Pre-select bed if already suggested
        $this->admissionDate = now()->format('Y-m-d'); // Default to today

        $this->loadInitialData();
    }

    /**
     * Load necessary data for the form.
     */
    public function loadInitialData(): void
    {
        $this->availableBeds = Bed::where('is_occupied', false)->get(['id', 'bed_number']);
    }

    /**
     * Update the existing admission record.
     */
    public function saveAdmission(): void
    {
        // 1. Validate the form input
        $this->validate();

        try {
            DB::connection('pgsql_transaction')->transaction(function () {
                // 2. Update the properties of the existing admission record
                $this->admission->fill([
                    'bed_id' => $this->bedId,
                    'reason_for_admission' => $this->reasonForAdmission,
                    'admission_date' => $this->admissionDate,
                    'status' => 'Admitted', // This is the key status change
                    'observation_fee' => $this->observationFee,
                    'admitted_by' => Auth::id(), // Track who admitted the patient
                ]);

                // 3. Save the updated admission record
                $this->admission->save();

                // 4. Mark the newly selected bed as occupied
                $bed = Bed::find($this->bedId);
                if ($bed) {
                    $bed->is_occupied = true;
                    $bed->save();
                }

                // 5. Log the activity
                $this->logActivity(
                    'Patient_Admission_Confirmed',
                    'Confirmed admission for patient ' . $this->patient->full_name,
                    [
                        'patient_id' => $this->patient->id,
                        'admission_id' => $this->admission->id,
                    ]
                );
            });
            // 6. Show success message and redirect
            LivewireAlert::title('Admission Confirmed!')
                ->success()
                ->text("Patient {$this->patient->full_name} has been successfully admitted.")
                ->show();

            $this->js('setTimeout(() => window.location.href = "' . route('receptionist.checkin') . '", 2500)');
        } catch (\Exception $e) {
            Log::error('Admission confirmation failed: ' . $e->getMessage());
            LivewireAlert::error('Error', 'Failed to admit patient. Please try again.')->show();
        }
    }

    /**
     * Render the component view.
     */
    public function render()
    {
        return view('livewire.tenants.receptionist.admit-patient');
    }
}
