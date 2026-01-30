<?php

namespace App\Http\Controllers\Api\Tenants\Admin;

use App\Http\Controllers\Controller;
use App\Services\Dashboards\AdminRevenueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminRevenueController extends Controller
{
    protected $revenueService;

    public function __construct(AdminRevenueService $revenueService)
    {
        $this->revenueService = $revenueService;
    }

    public function index(Request $request): JsonResponse
    {
        // Default to 'month' if not provided
        $period = $request->input('period', 'month');

        // Validate period
        if (! in_array($period, ['today', 'week', 'month', 'year'])) {
            $period = 'month';
        }

        $stats = $this->revenueService->getRevenueStats($period);
        $patientList = $this->revenueService->getPatientRevenueList($period, 10);

        return response()->json([
            'success' => true,
            'data' => [
                'period' => $period,
                'stats' => $stats,
                'top_patients' => $patientList,
            ],
        ]);
    }
}
