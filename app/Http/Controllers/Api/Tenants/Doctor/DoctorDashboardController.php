<?php

namespace App\Http\Controllers\Api\Tenants\Doctor;

use App\Http\Controllers\Controller;
use App\Services\Dashboards\DoctorDashboardService;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DoctorDashboardController extends Controller
{
    use HttpResponses;

    protected DoctorDashboardService $service;

    public function __construct(DoctorDashboardService $service)
    {
        $this->service = $service;
    }

    /**
     * Return JSON data for the Doctor Dashboard.
     */
    public function index(): JsonResponse
    {
        try {
            $this->logDebug('Fetching doctor dashboard data', [
                'user_id' => Auth::id(),
            ]);

            $data = $this->service->getDashboardData(Auth::id());

            $this->logInfo('Doctor dashboard data retrieved successfully', [
                'user_id' => Auth::id(),
            ]);

            return $this->success($data, 'Dashboard data retrieved.');
        } catch (\Exception $e) {
            $this->logException($e, [
                'user_id' => Auth::id(),
            ]);
            throw $e;
        }
    }
}
