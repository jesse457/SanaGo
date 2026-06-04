<?php

namespace App\Services\System;

use App\Models\Admission;
use App\Models\Appointment;
use App\Models\Bed;
use App\Models\EmergencyCase;
use App\Models\Invoice;
use App\Models\LabRequest;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Surgery;
use App\Services\Billing\RevenueService;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class DashboardService
{
    public function __construct(
        private RevenueService $revenue,
    ) {}

    public function getAllMetrics(): array
    {
        $today = now()->format('Y-m-d');

        return [
            'patient_stats' => $this->getPatientStats($today),
            'appointment_stats' => $this->getAppointmentStats($today),
            'clinical_stats' => $this->getClinicalStats(),
            'emergency_stats' => $this->getEmergencyStats(),
            'revenue_summary' => $this->revenue->getRevenueSummary($today, $today),
            'outstanding_invoices' => Invoice::whereIn('status', ['pending', 'overdue'])->sum('total_amount'),
            'outstanding_invoices_count' => Invoice::whereIn('status', ['pending', 'overdue'])->count(),
            'bed_occupancy' => $this->calculateBedOccupancy(),
            'monthly_data' => $this->revenue->getMonthlyRevenue((int) date('Y')),
            'recent_activity' => $this->getRecentActivity(),
        ];
    }

    private function getPatientStats(string $today): array
    {
        return [
            'total' => Patient::count(),
            'new_today' => Patient::whereDate('created_at', $today)->count(),
            'active_admissions' => Admission::whereIn('status', ['admitted', 'pending'])->count(),
        ];
    }

    private function getAppointmentStats(string $today): array
    {
        return [
            'total' => Appointment::whereDate('appointment_date', $today)->count(),
            'waiting' => Appointment::whereDate('appointment_date', $today)->where('status', 'waiting')->count(),
            'completed' => Appointment::whereDate('appointment_date', $today)->where('status', 'completed')->count(),
            'cancelled' => Appointment::whereDate('appointment_date', $today)->where('status', 'cancelled')->count(),
        ];
    }

    private function getClinicalStats(): array
    {
        return [
            'active_surgeries' => Surgery::where('status', 'in_progress')->count(),
            'pending_lab_requests' => LabRequest::where('status', 'pending')->count(),
            'pending_prescriptions' => Prescription::where('status', 'new')->count(),
        ];
    }

    private function getEmergencyStats(): array
    {
        return [
            'active_count' => EmergencyCase::whereIn('status', ['triage', 'active'])->count(),
        ];
    }

    private function calculateBedOccupancy(): array
    {
        $totalBeds = Bed::count();

        if ($totalBeds === 0) {
            return ['percentage' => 0, 'occupied' => 0, 'total' => 0];
        }

        $occupiedBeds = Bed::where('status', 'occupied')->count();

        return [
            'percentage' => round(($occupiedBeds / $totalBeds) * 100, 1),
            'occupied' => $occupiedBeds,
            'total' => $totalBeds,
        ];
    }

    private function getRecentActivity(): array
    {
        return Activity::with('causer')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn (Activity $log) => [
                'id' => $log->id,
                'description' => $log->description,
                'event' => $log->event,
                'causer_name' => $log->causer?->name ?? 'System',
                'created_at' => $log->created_at->diffForHumans(),
            ])
            ->toArray();
    }
}
