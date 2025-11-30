<?php

namespace App\Livewire\Tenants\Receptionist;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.receptionist')]
class Index extends Component
{
    // Dashboard statistics
    public $totalPatientsRegistered;
    public $appointmentsToday;
    public $pendingPaymentsList;
    public $appointmentsTodayConfirmed;
    public $appointmentsTodayPending;
    // Dropdown data for forms
    public $patients;
    public $doctors;



    /**
     * Initialize dashboard and dropdown data.
     */
    public function mount()
    {
        $this->loadDashboardData();
        $this->loadPatientsAndDoctors();
    }

    /**
     * Load dashboard statistics: total patients, today's appointments, pending payments.
     */
    public function loadDashboardData()
    {
        $today = Carbon::today();

        // Count total patients
        $this->totalPatientsRegistered = Patient::count();

        // Get today's appointments with related patient and doctor
        $this->appointmentsToday = Appointment::whereDate('appointment_date', $today)
            ->with(['patient', 'doctor'])
            ->get();
        // Get today's appointments with related patient and doctor
        $this->appointmentsTodayPending = Appointment::whereDate('appointment_date', $today)->where('status', 'Waiting')->count();
        $this->appointmentsTodayConfirmed = Appointment::whereDate('appointment_date', $today)->where('status', 'Confirmed')->count();
        $this->pendingPaymentsList = Invoice::whereIn('status', ['unpaid', 'partial'])
            ->with('patient')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    }

    /**
     * Load patients and doctors for dropdowns.
     */
    public function loadPatientsAndDoctors()
    {
        // Only select necessary columns for efficiency
        $this->patients = Patient::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();
        $this->doctors = User::select('id', 'name')->where('role', 'doctor')->orderBy('name')->get();
    }


    /**
     * Render the Livewire component view.
     */
    public function render()
    {
        return view('livewire.tenants.receptionist.index');
    }
}
