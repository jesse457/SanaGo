<?php

namespace App\Livewire\Tenants\Doctor;

use App\Livewire\Tenants\LabTechnician\Patients;
use App\Models\Appointment;
use App\Models\LabResult;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

// Use the doctor layout for this Livewire component
#[Layout('components.layouts.doctor')]
class Index extends Component
{
    // Properties to hold data for the doctor dashboard
    public Collection $upcomingAppointments;

    public Collection $patientsUnderCare;

    public Collection $incomingLabResults;

    // Called when the component is mounted
    public function mount()
    {
        $this->loadAppointments();
        $this->loadPatientsUnderCare();
        $this->loadIncomingLabResults();
    }

    // Load upcoming appointments for the authenticated doctor
    public function loadAppointments()
    {
        $doctorId = Auth::id(); // Get the currently authenticated doctor's ID
        $today = Carbon::today();

        $this->upcomingAppointments = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', '>=', $today)
            ->with('patient') // Eager load patient relationship
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();
    }

    // Load patients under the care of the authenticated doctor
    public function loadPatientsUnderCare()
    {
        $doctorId = Auth::id(); // Get the currently authenticated doctor's ID

        // Fetch patients who have had appointments or medical records with this doctor
        $this->patientsUnderCare = Patient::whereHas('admissions', function ($query) use ($doctorId) {
            $query->where('doctor_id', $doctorId);
        })
            ->distinct()
            ->get();
    }

    // Load incoming (completed) lab results requested by the authenticated doctor
    public function loadIncomingLabResults()
    {
        $doctorId = Auth::id(); // Get the currently authenticated doctor's ID
        $today = Carbon::today();
        $this->incomingLabResults = LabResult::where('doctor_id', Auth::id())
            ->with(['labRequest.patient', 'labRequest.testDefinition']) // Eager load relationships
            ->where('result_date', '>=', $today)
            ->orderBy('result_date', 'desc')
            ->limit(5) // Correct way to limit the result set
            ->get();
    }

    // Render the Livewire view for the doctor dashboard
    public function render()
    {
        // dd(LabResult::where('doctor_id',Auth::id()));
        return view('livewire.tenants.doctor.index');
    }
}
