<?php

namespace App\Http\Controllers\Api\Tenants\Nurse;

use App\Http\Controllers\Controller;
use App\Services\Dashboards\NurseDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NurseDashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(NurseDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Get nurse dashboard data.
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
