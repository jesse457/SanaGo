<?php

namespace App\Services\System;

use App\Enums\BedStatus;
use App\Models\Admission;
use App\Models\Appointment;
use App\Models\Bed;
use App\Models\Dispensation;
use App\Models\EmergencyCase;
use App\Models\Invoice;
use App\Models\LabRequest;
use App\Models\LabResult;
use App\Models\MedicalRecord;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\RadiologyRequest;
use App\Models\RadiologyResult;
use App\Models\Surgery;
use App\Services\Billing\RevenueService;
use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\Activitylog\Models\Activity;

class DashboardService
{
    public function __construct(
        private RevenueService $revenue,
    ) {}

    // ─── Admin (predefined — unchanged) ─────────────────────────────────

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

    // ─── Role-Agnostic Dashboard (non-admin roles) ──────────────────────

    /**
     * Build a role-aware dashboard config: which sections/metrics to show
     * based on the user's permissions, plus the filtered metric data.
     */
    public function getRoleDashboard(Authenticatable $user): array
    {
        $perms = $this->resolvePermissions($user);
        $today = now()->format('Y-m-d');

        return [
            'sections' => $this->buildSections($perms),
            'metrics' => $this->buildMetrics($perms, $today),
        ];
    }

    /**
     * Map the user's permission set to a simple keyed object.
     */
    private function resolvePermissions(Authenticatable $user): array
    {
        return [
            'canViewPatients' => $user->can('view-patients'),
            'canCreatePatients' => $user->can('create-patients'),
            'canViewAppointments' => $user->can('view-appointments'),
            'canCreateAppointments' => $user->can('create-appointments'),
            'canViewAdmissions' => $user->can('view-admissions'),
            'canCreateAdmissions' => $user->can('create-admissions'),
            'canDischargeAdmissions' => $user->can('discharge-admissions'),
            'canViewConsultations' => $user->can('view-consultations'),
            'canCreateConsultations' => $user->can('create-consultations'),
            'canViewVitals' => $user->can('view-vitals'),
            'canRecordVitals' => $user->can('record-vitals'),
            'canViewLabTests' => $user->can('view-lab-tests'),
            'canManageLabTests' => $user->can('manage-lab-tests'),
            'canViewLabResults' => $user->can('view-lab-results'),
            'canCreateLabResults' => $user->can('create-lab-results'),
            'canViewRadiology' => $user->can('view-radiology'),
            'canValidateRadiology' => $user->can('validate-radiology'),
            'canViewDrugs' => $user->can('view-drugs'),
            'canManageDrugs' => $user->can('manage-drugs'),
            'canDispenseDrugs' => $user->can('dispense-drugs'),
            'canViewSupplies' => $user->can('view-supplies'),
            'canManageSupplies' => $user->can('manage-supplies'),
            'canManageInfrastructure' => $user->can('manage-infrastructure'),
            'canManageDepartments' => $user->can('manage-departments'),
            'canViewFinance' => $user->can('view-finance'),
            'canManageCaisse' => $user->can('manage-caisse'),
            'canViewAuditLogs' => $user->can('view-audit-logs'),
            'canManageUsers' => $user->can('manage-users'),
            'canManageRoles' => $user->can('manage-roles'),
        ];
    }

    /**
     * Build the sections config that controls what the frontend renders.
     * Only sections relevant to the user's permissions are included.
     */
    private function buildSections(array $p): array
    {
        // ── Metric cards ──
        $metricCards = [];

        if ($p['canViewPatients']) {
            $metricCards[] = ['type' => 'patients', 'accent' => 'blue'];
        }

        if ($p['canViewAdmissions']) {
            $metricCards[] = ['type' => 'admissions', 'accent' => 'violet'];
        }

        if ($p['canViewAppointments']) {
            $metricCards[] = ['type' => 'appointments', 'accent' => 'amber'];
        }

        if ($p['canViewFinance']) {
            $metricCards[] = ['type' => 'revenue', 'accent' => 'emerald'];
        }

        if ($p['canViewDrugs'] || $p['canDispenseDrugs']) {
            $metricCards[] = ['type' => 'pharmacy_pending', 'accent' => 'teal'];
            $metricCards[] = ['type' => 'pharmacy_stock', 'accent' => 'amber'];
            $metricCards[] = ['type' => 'pharmacy_dispensed', 'accent' => 'blue'];
        }

        if ($p['canViewLabTests'] || $p['canViewLabResults']) {
            $metricCards[] = ['type' => 'lab_pending', 'accent' => 'purple'];
            $metricCards[] = ['type' => 'lab_completed', 'accent' => 'emerald'];
            $metricCards[] = ['type' => 'lab_in_progress', 'accent' => 'amber'];
        }

        if ($p['canCreateConsultations'] && ! $p['canViewFinance']) {
            $metricCards[] = ['type' => 'doctor_consultations', 'accent' => 'blue'];
            $metricCards[] = ['type' => 'doctor_lab_pending', 'accent' => 'purple'];
            $metricCards[] = ['type' => 'doctor_surgeries', 'accent' => 'rose'];
        }

        if ($p['canViewAppointments'] && ! $p['canViewFinance'] && ! $p['canCreateConsultations']) {
            $metricCards[] = ['type' => 'reception_waiting', 'accent' => 'amber'];
            $metricCards[] = ['type' => 'reception_new_patients', 'accent' => 'blue'];
            $metricCards[] = ['type' => 'reception_beds', 'accent' => 'violet'];
        }

        // ── Clinical chips ──
        $clinicalChips = [];

        if ($p['canCreateConsultations']) {
            $clinicalChips[] = 'surgeries';
        }

        if ($p['canViewLabTests'] || $p['canViewLabResults']) {
            $clinicalChips[] = 'lab';
        }

        if ($p['canCreateConsultations']) {
            $clinicalChips[] = 'prescriptions';
        }

        if ($p['canCreateAdmissions']) {
            $clinicalChips[] = 'emergencies';
        }

        if ($p['canViewRadiology']) {
            $clinicalChips[] = 'radiology';
        }

        // ── Quick action buttons ──
        $quickActions = [];

        if ($p['canCreatePatients']) {
            $quickActions[] = [
                'label' => 'New Patient',
                'href' => '/patients/create',
                'icon' => 'users',
            ];
        }

        if ($p['canCreateAppointments']) {
            $quickActions[] = [
                'label' => 'Book Appointment',
                'href' => '/appointments/create',
                'icon' => 'calendar',
            ];
        }

        if ($p['canCreateConsultations']) {
            $quickActions[] = [
                'label' => 'New Prescription',
                'href' => '/prescriptions/create',
                'icon' => 'clipboard',
            ];
        }

        if ($p['canCreateAdmissions']) {
            $quickActions[] = [
                'label' => 'New Emergency',
                'href' => '/emergencies/create',
                'icon' => 'exclamation',
            ];
        }

        if ($p['canDispenseDrugs']) {
            $quickActions[] = [
                'label' => 'View Dispensations',
                'href' => '/pharmacy/dispensation',
                'icon' => 'clipboard',
            ];
        }

        if ($p['canViewLabTests']) {
            $quickActions[] = [
                'label' => 'View Lab Requests',
                'href' => '/lab/requests',
                'icon' => 'beaker',
            ];
        }

        if ($p['canViewLabResults']) {
            $quickActions[] = [
                'label' => 'Enter Results',
                'href' => '/lab/results',
                'icon' => 'beaker',
            ];
        }

        if ($p['canCreateConsultations']) {
            $quickActions[] = [
                'label' => 'New Consultation',
                'href' => '/consultations/create',
                'icon' => 'clipboard',
            ];
            $quickActions[] = [
                'label' => 'View My Appointments',
                'href' => '/appointments/calendar',
                'icon' => 'calendar',
            ];
        }

        if ($p['canViewRadiology']) {
            $quickActions[] = [
                'label' => 'Radiology Requests',
                'href' => '/radiology/requests',
                'icon' => 'camera',
            ];
        }

        return [
            'metricCards' => $metricCards,
            'clinicalChips' => $clinicalChips,
            'quickActions' => $quickActions,
            'showRevenueChart' => $p['canViewFinance'],
            'showProfitLoss' => $p['canViewFinance'],
            'showOutstandingInvoices' => $p['canViewFinance'],
            'showRevenueBreakdown' => $p['canViewFinance'],
            // Only admin can see audit-log-based recent activity
            'showRecentActivity' => $p['canViewAuditLogs'],
            'showRevenueLink' => $p['canViewFinance'],
            // Role-specific summary panels
            'showPharmacyPanel' => $p['canViewDrugs'] || $p['canDispenseDrugs'],
            'showLabPanel' => ($p['canViewLabTests'] || $p['canViewLabResults']) && ! $p['canViewFinance'],
            'showDoctorPanel' => $p['canCreateConsultations'] && ! $p['canViewFinance'],
            'showReceptionPanel' => $p['canViewAppointments'] && ! $p['canViewFinance'] && ! $p['canCreateConsultations'],
        ];
    }

    /**
     * Build filtered metrics for the role-agnostic dashboard.
     * Only queries what's relevant to the user's permissions.
     */
    private function buildMetrics(array $p, string $today): array
    {
        $metrics = [];

        // Patient stats
        if ($p['canViewPatients']) {
            $metrics['patient_stats'] = $this->getPatientStats($today);
        }

        // Appointment stats
        if ($p['canViewAppointments']) {
            $metrics['appointment_stats'] = $this->getAppointmentStats($today);
        }

        // Clinical stats
        if ($p['canViewConsultations'] || $p['canCreateConsultations'] || $p['canViewLabTests']) {
            $metrics['clinical_stats'] = $this->getClinicalStats();
        }

        // Emergency stats
        if ($p['canCreateAdmissions']) {
            $metrics['emergency_stats'] = $this->getEmergencyStats();
        }

        // Revenue
        if ($p['canViewFinance']) {
            $metrics['revenue_summary'] = $this->revenue->getRevenueSummary($today, $today);
            $metrics['outstanding_invoices'] = Invoice::whereIn('status', ['pending', 'overdue'])->sum('total_amount');
            $metrics['outstanding_invoices_count'] = Invoice::whereIn('status', ['pending', 'overdue'])->count();
            $metrics['monthly_data'] = $this->revenue->getMonthlyRevenue((int) date('Y'));
        }

        // Bed occupancy
        if ($p['canViewAdmissions'] || $p['canManageInfrastructure']) {
            $metrics['bed_occupancy'] = $this->calculateBedOccupancy();
        }

        // Lab stats
        if ($p['canViewLabTests'] || $p['canViewLabResults']) {
            $metrics['lab_stats'] = [
                'pending_requests' => LabRequest::where('status', 'pending')->count(),
                'completed_today' => LabResult::whereDate('created_at', $today)->count(),
                'in_progress' => LabRequest::where('status', 'in_progress')->count(),
            ];
        }

        // Pharmacy stats
        if ($p['canViewDrugs'] || $p['canDispenseDrugs']) {
            $metrics['pharmacy_stats'] = [
                'pending_prescriptions' => Prescription::whereIn('status', ['new', 'partial'])->count(),
                'dispensed_today' => Dispensation::whereDate('dispensed_at', $today)->count(),
                'low_stock_count' => Medication::where('is_active', true)
                    ->whereColumn('total_stock', '<=', 'min_stock_level')
                    ->count(),
            ];
        }

        // Radiology stats
        if ($p['canViewRadiology']) {
            $metrics['radiology_stats'] = [
                'pending' => RadiologyRequest::where('status', 'pending')->count(),
                'completed_today' => RadiologyResult::whereDate('created_at', $today)->count(),
            ];
        }

        // Doctor-specific stats
        if ($p['canCreateConsultations'] && ! $p['canViewFinance']) {
            $metrics['doctor_stats'] = [
                'consultations_today' => MedicalRecord::where('doctor_id', auth()->id())
                    ->whereDate('record_date', $today)->count(),
                'my_pending_lab' => LabRequest::where('doctor_id', auth()->id())
                    ->where('status', 'pending')->count(),
                'my_active_surgeries' => Surgery::where('surgeon_id', auth()->id())
                    ->where('status', 'in_progress')->count(),
                'my_appointments_today' => Appointment::where('doctor_id', auth()->id())
                    ->whereDate('appointment_date', $today)->count(),
            ];
        }

        // Reception-specific stats
        if ($p['canViewAppointments'] && ! $p['canViewFinance'] && ! $p['canCreateConsultations']) {
            $waitingCount = Appointment::whereDate('appointment_date', $today)
                ->where('status', 'waiting')->count();
            $metrics['reception_stats'] = [
                'waiting_patients' => $waitingCount,
                'new_patients_today' => Patient::whereDate('created_at', $today)->count(),
                'available_beds' => Bed::where('status', BedStatus::Available)->count(),
                'total_beds' => Bed::count(),
                'todays_check_ins' => Appointment::whereDate('appointment_date', $today)
                    ->where('status', 'completed')->count(),
            ];
        }

        // Recent activity — only for admin
        if ($p['canViewAuditLogs']) {
            $metrics['recent_activity'] = $this->getRecentActivity();
        }

        // ── Role-specific panel tables ──
        if ($p['canViewDrugs'] || $p['canDispenseDrugs']) {
            $metrics['pharmacy_panel'] = $this->getPharmacyPanelData();
        }

        if (($p['canViewLabTests'] || $p['canViewLabResults']) && ! $p['canViewFinance']) {
            $metrics['lab_panel'] = $this->getLabPanelData();
        }

        if ($p['canCreateConsultations'] && ! $p['canViewFinance']) {
            $metrics['doctor_panel'] = $this->getDoctorPanelData();
        }

        if ($p['canViewAppointments'] && ! $p['canViewFinance'] && ! $p['canCreateConsultations']) {
            $metrics['reception_panel'] = $this->getReceptionPanelData($today);
        }

        if ($p['canViewAppointments'] && ($p['canViewFinance'] || $p['canCreateConsultations'])) {
            $metrics['appointment_panel'] = $this->getAppointmentPanelData($today);
        }

        return $metrics;
    }

    // ─── Panel table data providers ───────────────────────────────────

    private function getPharmacyPanelData(): array
    {
        return [
            'low_stock' => Medication::where('is_active', true)
                ->whereColumn('total_stock', '<=', 'min_stock_level')
                ->orderBy('total_stock')
                ->take(5)
                ->get(['id', 'name', 'total_stock', 'min_stock_level'])
                ->toArray(),
            'pending_dispense' => Prescription::with(['patient:id,first_name,last_name'])
                ->withCount('items')
                ->whereIn('status', ['new', 'partial'])
                ->orderBy('prescription_date', 'desc')
                ->take(5)
                ->get()
                ->map(fn ($rx) => [
                    'id' => $rx->id,
                    'patient_name' => $rx->patient?->first_name.' '.$rx->patient?->last_name,
                    'date' => $rx->prescription_date->format('Y-m-d'),
                    'items_count' => $rx->items_count,
                ])
                ->toArray(),
        ];
    }

    private function getLabPanelData(): array
    {
        return [
            'pending' => LabRequest::with(['patient:id,first_name,last_name', 'testDefinition:id,test_name'])
                ->where('status', 'pending')
                ->orderBy('request_date', 'desc')
                ->take(5)
                ->get()
                ->map(fn ($lr) => [
                    'id' => $lr->id,
                    'patient_name' => $lr->patient?->first_name.' '.$lr->patient?->last_name,
                    'test_name' => $lr->testDefinition?->test_name ?? '—',
                    'urgency' => $lr->urgency,
                    'date' => $lr->request_date->format('Y-m-d H:i'),
                ])
                ->toArray(),
            'in_progress' => LabRequest::where('status', 'in_progress')->count(),
        ];
    }

    private function getDoctorPanelData(): array
    {
        $doctorId = auth()->id();

        return [
            'today_appointments' => Appointment::with(['patient:id,first_name,last_name,patient_uid'])
                ->where('doctor_id', $doctorId)
                ->whereDate('appointment_date', today())
                ->orderBy('appointment_time')
                ->take(5)
                ->get()
                ->map(fn ($a) => [
                    'id' => $a->id,
                    'patient_name' => $a->patient?->first_name.' '.$a->patient?->last_name,
                    'time' => $a->appointment_time,
                    'status' => $a->status,
                ])
                ->toArray(),
            'recent_consults' => MedicalRecord::with(['patient:id,first_name,last_name'])
                ->where('doctor_id', $doctorId)
                ->orderBy('record_date', 'desc')
                ->take(5)
                ->get()
                ->map(fn ($m) => [
                    'id' => $m->id,
                    'patient_name' => $m->patient?->first_name.' '.$m->patient?->last_name,
                    'diagnosis' => $m->diagnosis,
                    'date' => $m->record_date->format('Y-m-d'),
                ])
                ->toArray(),
        ];
    }

    private function getReceptionPanelData(string $today): array
    {
        return [
            'waiting' => Appointment::with(['patient:id,first_name,last_name,patient_uid', 'doctor:id,name'])
                ->whereDate('appointment_date', $today)
                ->where('status', 'waiting')
                ->orderBy('appointment_time')
                ->take(8)
                ->get()
                ->map(fn ($a) => [
                    'id' => $a->id,
                    'patient_name' => $a->patient?->first_name.' '.$a->patient?->last_name,
                    'patient_uid' => $a->patient?->patient_uid,
                    'time' => $a->appointment_time,
                    'doctor_name' => $a->doctor?->name ?? '—',
                ])
                ->toArray(),
            'recent_registrations' => Patient::orderBy('created_at', 'desc')
                ->take(5)
                ->get(['id', 'first_name', 'last_name', 'patient_uid', 'phone', 'created_at'])
                ->toArray(),
        ];
    }

    private function getAppointmentPanelData(string $today): array
    {
        return [
            'upcoming' => Appointment::with(['patient:id,first_name,last_name', 'doctor:id,name'])
                ->whereDate('appointment_date', '>=', $today)
                ->where('status', '!=', 'cancelled')
                ->orderBy('appointment_date')
                ->orderBy('appointment_time')
                ->take(6)
                ->get()
                ->map(fn ($a) => [
                    'id' => $a->id,
                    'patient_name' => $a->patient?->first_name.' '.$a->patient?->last_name,
                    'doctor_name' => $a->doctor?->name ?? '—',
                    'date' => $a->appointment_date->format('Y-m-d'),
                    'time' => $a->appointment_time,
                    'status' => $a->status,
                ])
                ->toArray(),
        ];
    }

    // ─── Private stat helpers (unchanged) ──────────────────────────────

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
