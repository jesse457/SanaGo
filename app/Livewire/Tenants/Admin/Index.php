<?php

namespace App\Livewire\Tenants\Admin;

use App\Models\Admission;
use App\Models\Appointment;
use App\Models\Bed;
use App\Models\Department;
use App\Models\Supply;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class Index extends Component
{
    public $greeting;

    public $userName;

    public $userAvatar;

    public $dailyTotalRevenue = 0;

    public $totalPatientsAdmittedToday = 0;

    public $totalAppointmentsToday = 0;

    public $totalBeds = 0;

    public $totalBedsOccupied = 0;

    public $lowStockCount = 0;

    public $patientFlowLabels = [];

    public $patientFlowData = [];

    public $encounterSummaryLabels = [];

    public $encounterSummaryData = [];

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
        $this->userAvatar = $user->profile_picture ?? 'https://ui-avatars.com/api/?name='.urlencode($this->userName).'&color=7F9CF5&background=EBF4FF';
    }

    public function loadDashboardData()
    {
        $tenantId = tenant('id');

        // 1. Metrics (10 min cache)
        $metrics = Cache::remember("admin_metrics_{$tenantId}", 600, function () {
            $today = Carbon::today();

            return [
                'rev_appt' => Appointment::whereDate('appointment_date', $today)->where('status', 'Completed')->sum('price'),
                'rev_adm' => Admission::whereDate('created_at', $today)->sum('observation_fee'),
                'adm_today' => Admission::whereDate('created_at', $today)->count(),
                'appt_today' => Appointment::whereDate('appointment_date', $today)->count(),
            ];
        });
        $this->dailyTotalRevenue = $metrics['rev_appt'] + $metrics['rev_adm'];
        $this->totalPatientsAdmittedToday = $metrics['adm_today'];
        $this->totalAppointmentsToday = $metrics['appt_today'];

        // 2. Inventory (30 min cache)
        $inv = Cache::remember("admin_inv_{$tenantId}", 1800, function () {
            return [
                'total_beds' => Bed::count(),
                'occ_beds' => Bed::where('is_occupied', true)->count(),
                'low_stock' => Supply::whereColumn('current_stock', '<=', 'min_stock_level')->count(),
            ];
        });
        $this->totalBeds = $inv['total_beds'];
        $this->totalBedsOccupied = $inv['occ_beds'];
        $this->lowStockCount = $inv['low_stock'];

        // 3. Patient Flow Chart - 6 Months (1 hour cache)
        // DB Agnostic approach: Run 6 simple queries. Caching makes this instant.
        $flow = Cache::remember("admin_flow_{$tenantId}", 3600, function () {
            $labels = [];
            $data = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $labels[] = $month->format('M');
                $data[] = Appointment::whereMonth('appointment_date', $month->month)
                    ->whereYear('appointment_date', $month->year)
                    ->count();
            }

            return compact('labels', 'data');
        });
        $this->patientFlowLabels = $flow['labels'];
        $this->patientFlowData = $flow['data'];

        // 4. Weekly Summary (30 min cache)
        $weekly = Cache::remember("admin_weekly_{$tenantId}", 1800, function () {
            $labels = [];
            $data = [];
            $start = Carbon::now()->startOfWeek();
            for ($i = 0; $i < 7; $i++) {
                $day = $start->copy()->addDays($i);
                $labels[] = $day->format('D');
                $data[] = Appointment::whereDate('appointment_date', $day)->count();
            }

            return compact('labels', 'data');
        });
        $this->encounterSummaryLabels = $weekly['labels'];
        $this->encounterSummaryData = $weekly['data'];

        // 5. System & Roles (2 hour cache)
        $system = Cache::remember("admin_sys_{$tenantId}", 7200, function () {
            $roles = ['doctor', 'nurse', 'pharmacist', 'admin', 'receptionist', 'lab-technician'];
            $summary = [];
            foreach ($roles as $role) {
                $total = User::where('role', $role)->count();
                if ($total > 0) {
                    $summary[] = [
                        'role_name' => $role,
                        'total_users' => $total,
                        'active_users' => User::where('role', $role)->where('is_active', true)->count(),
                    ];
                }
            }

            return [
                'doc_count' => User::where('role', 'doctor')->count(),
                'user_count' => User::count(),
                'dept_count' => Department::count(),
                'summary' => $summary,
            ];
        });
        $this->totalDoctors = $system['doc_count'];
        $this->totalSystemUsers = $system['user_count'];
        $this->totalDepartments = $system['dept_count'];
        $this->userRoleSummary = $system['summary'];

        // 6. Recent Admissions (5 min cache)
        $this->recentAdmissions = Cache::remember("admin_recent_adm_{$tenantId}", 300, function () {
            return Admission::with(['patient', 'bed.ward'])->latest()->take(5)->get();
        });
    }

    public function render()
    {
        return view('livewire.tenants.admin.index');
    }
}
