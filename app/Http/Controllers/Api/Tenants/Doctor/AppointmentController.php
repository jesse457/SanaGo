<?php

namespace App\Http\Controllers\Api\Tenants\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Services\AppointmentService;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    use HttpResponses;

    protected AppointmentService $service;

    public function __construct(AppointmentService $service)
    {
        $this->service = $service;
    }

    /**
     * Get doctor's schedule for a specific date.
     */
    public function index(Request $request): JsonResponse
    {
        $date = $request->get('date', now()->toDateString());
        $schedule = $this->service->getDoctorDailyScheduleGrouped(Auth::id(), $date);

        return $this->success(array_values($schedule), 'Schedule retrieved.');
    }

    /**
     * Start an appointment consultation.
     */
    public function start(Appointment $appointment): JsonResponse
    {
        try {
            $updated = $this->service->startConsultation($appointment, Auth::id());

            return $this->success(new AppointmentResource($updated->load('patient')), 'Consultation started.');
        } catch (\Exception $e) {
            return $this->error(null, $e->getMessage(), 422);
        }
    }

    /**
     * End an appointment consultation.
     */
    public function end(Appointment $appointment): JsonResponse
    {
        try {
            $updated = $this->service->endConsultation($appointment, Auth::id());

            return $this->success(new AppointmentResource($updated->load('patient')), 'Consultation ended.');
        } catch (\Exception $e) {
            return $this->error(null, $e->getMessage(), 422);
        }
    }
}
