<?php

namespace App\Http\Controllers\Api\Tenants\Pharmacist;

use App\Http\Controllers\Controller;
use App\Services\Dashboards\PharmacistDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PharmacistDashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(PharmacistDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Get pharmacist dashboard data.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $this->logDebug('Fetching pharmacist dashboard data', [
                'user_id' => Auth::id(),
            ]);

            $data = $this->dashboardService->getDashboardData();

            $this->logInfo('Pharmacist dashboard data retrieved successfully', [
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            $this->logException($e, [
                'user_id' => Auth::id(),
            ]);
            throw $e;
        }
    }

    /**
     * Get medications with search and pagination.
     */
    public function medications(Request $request): JsonResponse
    {
        try {
            $search = $request->get('search', '');
            $sortBy = $request->get('sort_by', 'name');
            $sortDirection = $request->get('sort_direction', 'asc');
            $perPage = $request->get('per_page', 10);

            $this->logDebug('Fetching medications', [
                'user_id' => Auth::id(),
                'search' => $search,
                'sort_by' => $sortBy,
                'sort_direction' => $sortDirection,
                'per_page' => $perPage,
            ]);

            $medications = $this->dashboardService->getMedications($search, $sortBy, $sortDirection, $perPage);

            $this->logInfo('Medications retrieved successfully', [
                'user_id' => Auth::id(),
                'search' => $search,
                'count' => $medications->total() ?? count($medications),
            ]);

            return response()->json([
                'success' => true,
                'data' => $medications,
            ]);
        } catch (\Exception $e) {
            $this->logException($e, [
                'user_id' => Auth::id(),
                'search' => $request->get('search', ''),
            ]);
            throw $e;
        }
    }
}
