<?php

namespace App\Livewire\Tenants\Receptionist;

use App\Services\Dashboards\ReceptionistDashboardService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.receptionist')]
class Index extends Component
{
    // Dashboard statistics
    public $totalPatientsRegistered;

    public $appointmentsTodayConfirmed;

    public $appointmentsTodayPending;

    // Lists
    public $appointmentsToday;

    public $pendingPaymentsList;

    // Dropdown data
    public $patients;

    public $doctors;

    /**
     * Initialize dashboard using the Service.
     * Laravel automatically injects the service here.
     */
    public function mount(ReceptionistDashboardService $service)
    {
        // 1. Load Dashboard Data (Stats & Tables
        $dashboardData = $service->getDashboardData();

        $this->totalPatientsRegistered = $dashboardData['total_patients'];
        $this->appointmentsTodayConfirmed = $dashboardData['today_confirmed_count'];
        $this->appointmentsTodayPending = $dashboardData['today_pending_count'];
        $this->appointmentsToday = $dashboardData['appointments_today'];
        $this->pendingPaymentsList = $dashboardData['pending_payments'];

        // 2. Load Form Dropdowns
        $dropdownData = $service->getFormDropdowns();

        $this->patients = $dropdownData['patients'];
        $this->doctors = $dropdownData['doctors'];
    }

    public function render()
    {
        return view('livewire.tenants.receptionist.index');
    }
}
