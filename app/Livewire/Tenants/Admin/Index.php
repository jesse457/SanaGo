<?php

namespace App\Livewire\Tenants\Admin;

use App\Models\Admission;
use App\Models\Appointment;
use App\Models\Bed;
use App\Models\Department;
use App\Models\Patient;
use App\Models\Supply;
use App\Models\User; // Added Supply Model
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Index extends Component
{
    // User Info
    public $greeting;

    public $userName;

    public $userAvatar;

    // Metric Cards
    public $dailyTotalRevenue = 0;

    public $totalPatientsAdmittedToday = 0;

    public $totalAppointmentsToday = 0;

    public $totalBeds = 0;

    public $totalBedsOccupied = 0;

    public $lowStockCount = 0; // New Metric

    // Chart Data
    public $patientFlowLabels = [];

    public $patientFlowData = [];

    public $encounterSummaryLabels = [];

    public $encounterSummaryData = [];

    // Bottom Section
    public $totalDoctors = 0;

    public $totalSystemUsers = 0;

    public $totalDepartments = 0;

    public $userRoleSummary = [];

    public $recentAdmissions = [];

    public function mount()
    {
        $this->setGreeting();
        $this->loadDashboardData();
    }

    public function setGreeting()
    {
        $hour = date('H');
        $this->greeting = match (true) {
            $hour < 12 => 'Good Morning',
            $hour < 18 => 'Good Afternoon',
            default => 'Good Evening',
        };

        $user = Auth::user();
        $this->userName = $user->name ?? 'Administrator';
        $this->userAvatar = $user->profile_picture
            ?? 'https://ui-avatars.com/api/?name='.urlencode($this->userName).'&color=7F9CF5&background=EBF4FF';
    }

    public function loadDashboardData()
    {
        // 1. Metrics
        $today = Carbon::today();

        // Revenue: Sum of Appointment Prices (Completed) + Admission Observation Fees
        // Assuming 'price' and 'observation_fee' are numeric
        $apptRevenue = Appointment::whereDate('appointment_date', $today)
            ->where('status', 'Completed')
            ->sum('price');

        $admissionRevenue = Admission::whereDate('created_at', $today)
            ->sum('observation_fee');

        $this->dailyTotalRevenue = $apptRevenue + $admissionRevenue;

        $this->totalPatientsAdmittedToday = Admission::whereDate('created_at', $today)->count();
        $this->totalAppointmentsToday = Appointment::whereDate('appointment_date', $today)->count();

        // Bed Stats
        $this->totalBeds = Bed::count();
        $this->totalBedsOccupied = Bed::where('is_occupied', true)->count();

        // Supply Stats (Low Stock)
        $this->lowStockCount = Supply::whereColumn('current_stock', '<=', 'min_stock_level')->count();

        // 2. Chart: Last 6 Months of Appointments
        $data = [];
        $labels = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M');
            $data[] = Appointment::whereMonth('appointment_date', $date->month)
                ->whereYear('appointment_date', $date->year)
                ->count();
        }
        $this->patientFlowLabels = $labels;
        $this->patientFlowData = $data;

        // 3. Encounter Summary (This Week)
        $this->totalDoctors = User::where('role', 'doctor')->count(); // Ensure 'role' matches your DB string exactly (e.g. 'Doctor' vs 'doctor')
        $this->totalSystemUsers = User::count();
        $this->totalDepartments = Department::count();

        $startOfWeek = Carbon::now()->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $day = $startOfWeek->copy()->addDays($i);
            $this->encounterSummaryLabels[] = $day->format('D'); // Mon, Tue...
            $this->encounterSummaryData[] = Appointment::whereDate('appointment_date', $day)->count();
        }

        // 4. Role Summary
        $roles = ['doctor', 'nurse', 'pharmacist', 'admin', 'receptionist', 'lab-technician'];
        foreach ($roles as $role) {
            $usersInRole = User::where('role', $role)->get();
            $count = $usersInRole->count();
            $active = $usersInRole->where('is_active', true)->count();

            if ($count > 0) {
                $this->userRoleSummary[] = [
                    'role_name' => $role,
                    'total_users' => $count,
                    'active_users' => $active,
                ];
            }
        }

        // 5. Recent Admissions (Eager load patient to avoid N+1)
        $this->recentAdmissions = Admission::with(['patient', 'bed.ward'])
            ->latest()
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.tenants.admin.index');
    }
}
