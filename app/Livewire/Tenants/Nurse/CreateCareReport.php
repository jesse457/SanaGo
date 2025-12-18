<?php

namespace App\Livewire\Tenants\Nurse;

use App\Models\NurseCareReport;
use App\Models\Patient;
use App\Models\Vital;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.nurse')]
class CreateCareReport extends Component
{
    // Form Properties
    public $patient_id = '';
    public $report_time;
    public $shift_type = 'Morning';
    public $interventions = '';
    public $observations = '';

    // Vitals Context (Optional)
    public $vitals_bp = '';
    public $vitals_hr = '';
    public $vitals_temp = '';
    public $vitals_spo2 = '';

    // SEARCH PROPERTIES
    public $patientSearch = '';
    public $searchResults = [];
    public $showDropdown = false;

    public function mount()
    {
        $this->report_time = now()->format('Y-m-d\TH:i');
    }

    public function rules()
    {
        return [
            'patient_id'    => 'required|exists:patients,id',
            'report_time'   => 'required|date',
            'shift_type'    => 'required|in:Morning,Afternoon,Night',
            'interventions' => 'required|string|min:5',
            'observations'  => 'required|string|min:5',
            'vitals_bp'     => 'nullable|string|max:20',
            'vitals_hr'     => 'nullable|integer|min:30|max:250',
            'vitals_temp'   => 'nullable|numeric|min:30|max:45',
            'vitals_spo2'   => 'nullable|integer|min:50|max:100',
        ];
    }

    /**
     * Search Logic: Runs when user types in the input
     */
    public function updatedPatientSearch()
    {
        if (strlen($this->patientSearch) < 2) {
            $this->searchResults = [];
            $this->showDropdown = false;
            return;
        }

        // Fetch patients and filter in PHP (supports encrypted fields if necessary)
        $allPatients = Patient::all();

        $this->searchResults = $allPatients->filter(function ($patient) {
            return str_contains(strtolower($patient->name), strtolower($this->patientSearch))
                || str_contains($patient->patient_uid, $this->patientSearch);
        })->take(10);

        $this->showDropdown = true;
    }

    /**
     * Select a patient from the search results
     */
    public function selectPatient($id, $name)
    {
        $this->patient_id = $id;
        $this->patientSearch = $name;
        $this->showDropdown = false;
    }

    public function saveCareReport()
    {
        $this->validate();

        DB::transaction(function () {
            // 1. Create Report
            NurseCareReport::create([
                'patient_id'    => $this->patient_id,
                'user_id'       => Auth::id(),
                'report_time'   => $this->report_time,
                'shift_type'    => $this->shift_type,
                'interventions' => $this->interventions,
                'observations'  => $this->observations,
            ]);

            // 2. Create Vitals (if provided)
            if ($this->vitals_bp || $this->vitals_hr || $this->vitals_temp || $this->vitals_spo2) {
                Vital::create([
                    'patient_id'        => $this->patient_id,
                    'user_id'           => Auth::id(),
                    'blood_pressure'    => $this->vitals_bp,
                    'heart_rate'        => $this->vitals_hr,
                    'temperature'       => $this->vitals_temp,
                    'oxygen_saturation' => $this->vitals_spo2,
                    'recorded_at'       => $this->report_time,
                ]);
            }
        });

        session()->flash('success', 'Patient care report submitted successfully.');
        $this->reset(['patient_id', 'patientSearch', 'interventions', 'observations', 'vitals_bp', 'vitals_hr', 'vitals_temp', 'vitals_spo2']);
        $this->report_time = now()->format('Y-m-d\TH:i');
    }

    public function cancelReport()
    {
        return $this->redirect(route('nurse.dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.tenants.nurse.create-care-report');
    }
}
