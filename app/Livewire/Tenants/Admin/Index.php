<?php

namespace App\Livewire\Tenants\Admin;

use App\Services\Dashboards\AdminDashboardService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Index extends Component
{
    // Greeting Props
    public $greeting;

    public $userName;

    public $userAvatar;

    // Metrics Props
    public $dailyTotalRevenue = 0;

    public $totalPatientsAdmittedToday = 0;

    public $totalAppointmentsToday = 0;

    public $totalBeds = 0;

    public $totalBedsOccupied = 0;

    public $lowStockCount = 0;

    // Chart Props
    public $patientFlowLabels = [];

    public $patientFlowData = [];

    public $encounterSummaryLabels = [];

    public $encounterSummaryData = [];

    // System Props
    public $totalDoctors = 0;

    public $totalSystemUsers = 0;

    public $totalDepartments = 0;

    public $userRoleSummary = [];

    // Table Props
    public $recentAdmissions = [];

    public function mount(AdminDashboardService $dashboardService)
    {
        $this->loadData($dashboardService);
    }

    public function loadData(AdminDashboardService $service)
    {
        // 1. Greeting
        $greetData = $service->getGreetingData(Auth::user());
        $this->greeting = $greetData['greeting'];
        $this->userName = $greetData['user_name'];
        $this->userAvatar = $greetData['user_avatar'];

        // 2. Daily Metrics
        $metrics = $service->getDailyMetrics();
        $this->dailyTotalRevenue = $metrics['total_revenue'];
        $this->totalPatientsAdmittedToday = $metrics['admissions_today'];
        $this->totalAppointmentsToday = $metrics['appointments_today'];

        // 3. Inventory
        $inv = $service->getInventoryMetrics();
        $this->totalBeds = $inv['total_beds'];
        $this->totalBedsOccupied = $inv['occupied_beds'];
        $this->lowStockCount = $inv['low_stock_count'];

        // 4. Charts
        $flow = $service->getPatientFlowChart();
        $this->patientFlowLabels = $flow['labels'];
        $this->patientFlowData = $flow['data'];

        $weekly = $service->getWeeklyEncounterChart();
        $this->encounterSummaryLabels = $weekly['labels'];
        $this->encounterSummaryData = $weekly['data'];

        // 5. System
        $sys = $service->getSystemOverview();
        $this->totalDoctors = $sys['total_doctors'];
        $this->totalSystemUsers = $sys['total_users'];
        $this->totalDepartments = $sys['total_departments'];
        $this->userRoleSummary = $sys['role_summary'];

        // 6. Recent Admissions
        $this->recentAdmissions = $service->getRecentAdmissions();
    }

    public function render()
    {
        return view('livewire.tenants.admin.index');
    }
}
