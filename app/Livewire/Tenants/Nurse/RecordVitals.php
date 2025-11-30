<?php

namespace App\Livewire\Tenants\Nurse;

use App\Models\Patient;
use App\Models\Vital;
use App\Traits\UserActivitiesTrait; // Assuming you have a Patient model
use Illuminate\Support\Facades\Auth;   // The Vital model
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
// Import for conditional validation rules
use Livewire\Component;

#[Layout('components.layouts.nurse')]
class RecordVitals extends Component
{
    use UserActivitiesTrait;

    // --- Patient Selection Properties ---
    public $patients = [];          // Holds the list of all patients for the dropdown

    public $selectedPatientId = null; // Stores the ID of the currently selected patient

    public $selectedPatientName = 'N/A'; // Displays the name of the selected patient

    // --- New Patient Registration Properties ---
    public $showNewPatientForm = false; // Toggle to show/hide new patient form

    public $newPatient = [
        'first_name' => '',
        'last_name' => '',
        'date_of_birth' => '',
        'gender' => '',
        'contact_number' => '',
        'address' => '',
        'patient_uid' => '', // Unique identifier for the patient
    ];

    // --- Vital Properties ---
    public $bloodPressure = '';

    public $temperature = null;

    public $heartRate = null;

    public $oxygenSaturation = null;

    public $nurseNotes = '';

    public $respiratoryRate = null;

    public $weightKg = null;

    public $heightCm = null;

    public $flagAbnormal = false;

    public $showSuccessModal = false;

    public $showErrorModal = false;

    public function mount()
    {
        // Fetch all patients for the dropdown as before
        $this->patients = Patient::orderBy('first_name')->orderBy('last_name')->get();

        // Check if a patient ID was flashed from the dashboard
        if (Session::has('selectedPatientId')) {
            $patientId = Session::get('selectedPatientId');
            $patient = Patient::find($patientId);

            if ($patient) {
                $this->selectedPatientId = $patient->id;
                $this->selectedPatientName = $patient->first_name . ' ' . $patient->last_name;
            }
        }
    }

    /**
     * Livewire lifecycle hook: Called when $selectedPatientId property changes.
     */
    public function updatedSelectedPatientId()
    {
        // When an existing patient is selected, hide the new patient form
        $this->showNewPatientForm = false;
        $this->resetNewPatientForm(); // Clear any existing new patient data

        // Reset vital sign fields when a new patient is selected
        $this->resetVitalFields();
        $this->resetValidation(); // Clear any existing validation errors

        if ($this->selectedPatientId) {
            $patient = Patient::find($this->selectedPatientId);
            if ($patient) {
                $this->selectedPatientName = $patient->first_name . ' ' . $patient->last_name;
            } else {
                $this->selectedPatientName = 'N/A';
                $this->selectedPatientId = null; // Reset if patient not found
                $this->dispatch('show-error-modal', message: 'Selected patient not found.');
            }
        } else {
            $this->selectedPatientName = 'N/A';
        }
    }

    /**
     * Toggles the visibility of the new patient registration form.
     */
    public function toggleNewPatientForm()
    {
        $this->showNewPatientForm = ! $this->showNewPatientForm;
        if ($this->showNewPatientForm) {
            $this->selectedPatientId = null; // Deselect any existing patient
            $this->selectedPatientName = 'N/A';
            $this->resetVitalFields();
        } else {
            $this->resetNewPatientForm();
        }
        $this->resetValidation(); // Clear validation errors
    }

    /**
     * Saves the recorded vital signs and notes.
     */
    public function saveVitals()
    {

        // Add validation to ensure a patient is selected
        $this->validate([
            'selectedPatientId' => 'required|exists:patients,id', // Ensure a valid patient is selected
            'bloodPressure' => ['required', 'string', 'regex:/^\d{2,3}\/\d{2,3}$/'],
            'temperature' => 'required|numeric|min:35.0|max:42.0',
            'heartRate' => 'required|integer|min:30|max:200',
            'oxygenSaturation' => 'required|integer|min:0|max:100',
            'respiratoryRate' => 'nullable|integer|min:8|max:40',
            'weightKg' => 'nullable|numeric|min:1|max:500',
            'heightCm' => 'nullable|numeric|min:50|max:250',
            'nurseNotes' => 'nullable|string|max:1000',
            'flagAbnormal' => 'boolean',
        ], [
            'selectedPatientId.required' => 'Please select a patient.',
            'selectedPatientId.exists' => 'The selected patient does not exist.',
            'bloodPressure.regex' => 'Blood pressure must be in format like 120/80.',
            'temperature.min' => 'Temperature must be at least 35.0°C.',
            'temperature.max' => 'Temperature must not exceed 42.0°C.',
            'oxygenSaturation.min' => 'Oxygen Saturation cannot be negative.',
            'oxygenSaturation.max' => 'Oxygen Saturation cannot exceed 100%.',
            'respiratoryRate.min' => 'Respiratory rate must be at least 8.',
            'respiratoryRate.max' => 'Respiratory rate cannot exceed 40.',
            'weightKg.min' => 'Weight must be at least 1 kg.',
            'weightKg.max' => 'Weight cannot exceed 500 kg.',
            'heightCm.min' => 'Height must be at least 50 cm.',
            'heightCm.max' => 'Height cannot exceed 250 cm.',
        ]);

        [$systolic, $diastolic] = explode('/', $this->bloodPressure);

        $bmi = null;
        if ($this->weightKg && $this->heightCm && $this->heightCm > 0) {
            $heightInMeters = $this->heightCm / 100;
            $bmi = round($this->weightKg / ($heightInMeters * $heightInMeters), 2);
        }

        try {
            Vital::create([
                'patient_id' => $this->selectedPatientId, // Use the selected ID
                'nurse_id' => Auth::id(),
                'recorded_at' => now(),
                'temperature_celsius' => $this->temperature,
                'blood_pressure_systolic' => (int) $systolic,
                'blood_pressure_diastolic' => (int) $diastolic,
                'heart_rate_bpm' => $this->heartRate,
                'spo2_percentage' => $this->oxygenSaturation,
                'respiratory_rate' => $this->respiratoryRate,
                'weight_kg' => $this->weightKg,
                'height_cm' => $this->heightCm,
                'bmi' => $bmi,
                'flag_abnormal' => $this->flagAbnormal,
                'notes' => $this->nurseNotes,

            ]);
            $nurse = Auth::user()->name;
            $this->logActivity(
                'vitals_recorded',
                "{$nurse} recorded vitals for {$this->selectedPatientName} ",
                [
                    'nurse_id' => Auth::id(),
                    'patient_id' => $this->selectedPatientId,
                ]
            );
            LivewireAlert::title('Success')->success()->text('Vitals saved successfully')->show();

            // Reset all form fields, but keep the selected patient if desired
            $this->resetVitalFields();
            $this->resetValidation(); // Clear validation errors

        } catch (\Exception $e) {
            Log::error('Error saving vitals: ' . $e->getMessage(), ['patient_id' => $this->selectedPatientId, 'user_id' => Auth::id()]);
        }
    }

    // --- Helper Methods for Resetting ---
    private function resetVitalFields()
    {
        $this->reset([
            'bloodPressure',
            'temperature',
            'heartRate',
            'oxygenSaturation',
            'respiratoryRate',
            'weightKg',
            'heightCm',
            'flagAbnormal',
            'nurseNotes',
        ]);
    }

    private function resetNewPatientForm()
    {
        $this->newPatient = [
            'first_name' => '',
            'last_name' => '',
            'date_of_birth' => '',
            'gender' => '',
            'contact_number' => '',
            'address' => '',
            'patient_uid' => '',
        ];
    }

    public function render()
    {
        return view('livewire.tenants.nurse.record-vitals');
    }
}
