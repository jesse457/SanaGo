<?php

namespace App\Http\Controllers\Api\Tenants\LabTechnician;

use App\Http\Controllers\Controller;
use App\Services\Dashboards\LabTechnicianDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LabTechnicianDashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(LabTechnicianDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Get lab technician dashboard data.
     */
    public function index(Request $request): JsonResponse
    {
        $data = $this->dashboardService->getDashboardData();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
