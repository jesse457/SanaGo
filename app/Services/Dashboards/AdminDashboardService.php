<?php

namespace App\Services\Dashboards;

use App\Models\Admission;
use App\Models\Appointment;
use App\Models\Bed;
use App\Models\Department;
use App\Models\Supply;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AdminDashboardService
{
    /**
     * Get the greeting and user profile data.
     */
    public function getGreetingData(User $user): array
    {
        $hour = (int) date('H');

        $greeting = match (true) {
            $hour < 12 => 'Good Morning',
            $hour < 18 => 'Good Afternoon',
            default => 'Good Evening',
        };

        $userName = $user->name ?? 'Administrator';
        $userAvatar = $user->profile_picture
            ?? 'https://ui-avatars.com/api/?name='.urlencode($userName).'&color=7F9CF5&background=EBF4FF';

        return [
            'greeting' => $greeting,
            'user_name' => $userName,
            'user_avatar' => $userAvatar,
        ];
    }

    /**
     * Get Daily Revenue and Patient Counts (10 min cache).
     */
    public function getDailyMetrics(): array
    {
        $tenantId = tenant('id');

        return Cache::remember("admin_metrics_{$tenantId}", 600, function () {
            $today = Carbon::today();

            $revAppt = Appointment::whereDate('appointment_date', $today)
                ->where('status', 'Completed')
                ->sum('price');

            $revAdm = Admission::whereDate('created_at', $today)
                ->sum('observation_fee');

            return [
                'total_revenue' => $revAppt + $revAdm,
                'admissions_today' => Admission::whereDate('created_at', $today)->count(),
                'appointments_today' => Appointment::whereDate('appointment_date', $today)->count(),
            ];
        });
    }

    /**
     * Get Bed Occupancy and Inventory Status (30 min cache).
     */
    public function getInventoryMetrics(): array
    {
        $tenantId = tenant('id');

        return Cache::remember("admin_inv_{$tenantId}", 1800, function () {
            return [
                'total_beds' => Bed::count(),
                'occupied_beds' => Bed::where('is_occupied', true)->count(),
                'low_stock_count' => Supply::whereColumn('current_stock', '<=', 'min_stock_level')->count(),
            ];
        });
    }

    /**
     * Get 6-month Patient Flow Chart Data (1 hour cache).
     */
    public function getPatientFlowChart(): array
    {
        $tenantId = tenant('id');

        return Cache::remember("admin_flow_{$tenantId}", 3600, function () {
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
    }

    /**
     * Get Weekly Encounter Chart Data (30 min cache).
     */
    public function getWeeklyEncounterChart(): array
    {
        $tenantId = tenant('id');

        return Cache::remember("admin_weekly_{$tenantId}", 1800, function () {
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
    }

    /**
     * Get System Users and Role Summary (2 hour cache).
     */
    public function getSystemOverview(): array
    {
        $tenantId = tenant('id');

        return Cache::remember("admin_sys_{$tenantId}", 7200, function () {
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
                'total_doctors' => User::where('role', 'doctor')->count(),
                'total_users' => User::count(),
                'total_departments' => Department::count(),
                'role_summary' => $summary,
            ];
        });
    }

    /**
     * Get Recent Admissions (5 min cache).
     */
    public function getRecentAdmissions()
    {
        $tenantId = tenant('id');

        return Cache::remember("admin_recent_adm_{$tenantId}", 300, function () {
            return Admission::with(['patient', 'bed.ward'])
                ->latest()
                ->take(5)
                ->get();
        });
    }

    /**
     * Aggregator method to get all data at once (useful for API).
     */
    public function getAllDashboardData(User $user): array
    {
        return [
            'greeting_data' => $this->getGreetingData($user),
            'metrics' => $this->getDailyMetrics(),
            'inventory' => $this->getInventoryMetrics(),
            'patient_flow' => $this->getPatientFlowChart(),
            'encounters' => $this->getWeeklyEncounterChart(),
            'system' => $this->getSystemOverview(),
            'recent_admissions' => $this->getRecentAdmissions(),
        ];
    }
}
