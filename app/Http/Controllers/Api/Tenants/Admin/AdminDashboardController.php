<?php

namespace App\Http\Controllers\Api\Tenants\Admin;

use App\Http\Controllers\Controller;
use App\Services\Dashboards\AdminDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(AdminDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Get full dashboard statistics.
     */
    public function index(Request $request): JsonResponse
    {
        $data = $this->dashboardService->getAllDashboardData($request->user());

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get specific chart data (example of granular fetching).
     */
    public function charts(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'patient_flow' => $this->dashboardService->getPatientFlowChart(),
                'weekly_encounters' => $this->dashboardService->getWeeklyEncounterChart(),
            ],
        ]);
    }
}
