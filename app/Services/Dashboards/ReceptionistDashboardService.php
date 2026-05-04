<?php

namespace App\Services\Dashboards;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;

class ReceptionistDashboardService
{
    /**
     * Retrieve main dashboard statistics and lists.
     */
    public function getDashboardData(): array
    {
        $today = Carbon::today();

        // 1. Statistics
        $totalPatients = Patient::count();

        $todayPendingCount = Appointment::whereDate('appointment_date', $today)
            ->where('status', 'Waiting')
            ->count();

        $todayConfirmedCount = Appointment::whereDate('appointment_date', $today)
            ->where('status', 'Confirmed')
            ->count();

        // 2. Lists (Tables)
        $appointmentsToday = Appointment::whereDate('appointment_date', $today)
            ->with(['patient', 'doctor']) // Eager load relationships
            ->orderBy('appointment_time', 'asc')
            ->get();

        $pendingPayments = Invoice::whereIn('status', ['unpaid', 'partial'])
            ->with('patient')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return [
            'total_patients' => $totalPatients,
            'today_pending_count' => $todayPendingCount,
            'today_confirmed_count' => $todayConfirmedCount,
            'appointments_today' => $appointmentsToday,
            'pending_payments' => $pendingPayments,
        ];
    }

    public function getDoctorsList()
    {
        $fetchDoctors = function () {
            return \App\Models\User::query()
                ->where('role', 'doctor')
                ->select(['id', 'name', 'email', 'department_id'])
                ->with(['department:id,name'])
                ->orderBy('name')
                ->get()
                ->map(fn ($d) => [
                    'id' => $d->id,
                    'name' => $d->name,
                    'email' => $d->email,
                    'department' => $d->department?->name,
                ])->toArray();
        };

        return \Illuminate\Support\Facades\Cache::remember('doctors_list_shared', 60, $fetchDoctors);
    }

    /**
     * Retrieve dropdown options for forms (Patients and Doctors).
     */
    public function getFormDropdowns(): array
    {
        // Select specific columns to optimize performance
        $patients = Patient::select('id', 'first_name', 'last_name')
            ->orderBy('first_name')
            ->get();

        $doctors = User::select('id', 'name')
            ->where('role', 'doctor')
            ->orderBy('name')
            ->get();

        return [
            'patients' => $patients,
            'doctors' => $doctors,
        ];
    }
}
