<?php

namespace App\Services\Dashboards;

use App\Models\LabRequest;
use App\Models\LabResult;
use Carbon\Carbon;

class LabTechnicianDashboardService
{
    /**
     * Retrieve main dashboard statistics and lists for lab technicians.
     */
    public function getDashboardData(): array
    {
        $today = Carbon::today();

        // 1. Pending List (Data actually needed for the table)
        // Optimization: Select specific columns and eager load specific columns
        $pendingLabRequests = LabRequest::query()
            ->select([
                'id',
                'patient_id',
                'lab_test_definition_id',
                'requested_by_doctor_id',
                'urgency_level',
                'request_date',
                'status'
            ])
            ->where('status', 'requested')
            ->with([
                'patient',
                'testDefinition',
                'doctor:id,name'
            ])
            ->orderBy('request_date', 'desc')
            ->get();

        // 2. Statistics - Completed Today
        // Optimization: Use count() directly in SQL, remove unnecessary eager loads
        $completedTestsCount = LabResult::query()
            ->whereDate('result_date', $today)
            ->count();

        // 3. Statistics - In Progress
        // Optimization: Use count() directly in SQL, remove unnecessary eager loads
        $inProgressTestsCount = LabRequest::query()
            ->whereDate('request_date', $today)
            ->where('status', 'In_Progress')
            ->count();

        return [
            'pending_lab_requests' => $pendingLabRequests,
            'completed_tests_count' => $completedTestsCount, // Returning integer
            'in_progress_count' => $inProgressTestsCount,     // Returning integer
        ];
    }
}
