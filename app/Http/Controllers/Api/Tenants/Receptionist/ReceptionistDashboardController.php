<?php

namespace App\Http\Controllers\Api\Tenants\Receptionist;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource; // Uses the resource we created
use App\Services\Dashboards\ReceptionistDashboardService;
use Illuminate\Http\JsonResponse;

class ReceptionistDashboardController extends Controller
{
    /**
     * Display the receptionist dashboard data.
     *
     * The Service is automatically injected by Laravel.
     */
    public function index(ReceptionistDashboardService $service): JsonResponse
    {
        // 1. Fetch data from the shared service
        $dashboardData = $service->getDashboardData();
        $dropdownData = $service->getFormDropdowns();

        // 2. Prepare the response
        // We use AppointmentResource to format the date/time/relationships nicely
        return response()->json([
            'stats' => [
                'total_patients' => $dashboardData['total_patients'],
                'today_pending' => $dashboardData['today_pending_count'],
                'today_confirmed' => $dashboardData['today_confirmed_count'],
            ],
            'tables' => [
                'appointments_today' => AppointmentResource::collection($dashboardData['appointments_today']),
                // You can create an InvoiceResource later if needed, for now standard JSON is fine
                'pending_payments' => $dashboardData['pending_payments'],
            ],
            'dropdowns' => [
                'patients' => $dropdownData['patients'],
                'doctors' => $dropdownData['doctors'],
            ],
        ]);
    }
}
