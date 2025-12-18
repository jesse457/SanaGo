<?php

namespace App\Livewire\Tenants\Nurse;

use App\Models\NurseCareReport;
use App\Models\Patient;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.nurse')]
class ViewCareReports extends Component
{
    public $patient_id = '';
    public Collection $patients;
    public $reports = [];

    public function mount()
    {
        $this->loadPatients();
    }

    public function loadPatients()
    {
        // For the dropdown filter
        $this->patients = Patient::query()
            ->get()
            ->sortBy('last_name', SORT_NATURAL | SORT_FLAG_CASE);
    }

    // Automatically run this whenever $patient_id changes in the dropdown
    public function updatedPatientId()
    {
        if ($this->patient_id) {
            $this->reports = NurseCareReport::query()
                ->where('patient_id', $this->patient_id)
                ->with('nurse')
                ->latest('report_time')
                ->get();
        } else {
            $this->reports = [];
        }
    }

    public function render()
    {
        return view('livewire.tenants.nurse.view-care-reports');
    }
}
