<?php

namespace App\Http\Controllers\Api\Tenants\Receptionist;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AppointmentController extends Controller
{
    protected AppointmentService $service;

    public function __construct(AppointmentService $service)
    {
        $this->service = $service;
    }

    /**
     * GET /api/receptionist/appointments
     * List all appointments with filters.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['date', 'doctor_id', 'status', 'search']);

        // Uses the service to get paginated results
        $appointments = $this->service->getPaginatedAppointments($filters, 15);

        return AppointmentResource::collection($appointments);
    }

    /**
     * POST /api/receptionist/appointments
     * Create a new appointment.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id', // Assuming doctors are in users table
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'reason' => 'nullable|string|max:255',
        ]);

        try {
            // Fetch models manually to pass to service, or let service handle IDs (refactor service if needed)
            // Assuming Service accepts Models based on previous code:
            $patient = \App\Models\Patient::findOrFail($validated['patient_id']);
            $doctor = \App\Models\User::findOrFail($validated['doctor_id']);

            $appointment = $this->service->createAppointment(
                $doctor,
                $patient,
                $validated['date'],
                $validated['time'],
                $validated['reason'] ?? null
            );

            return response()->json([
                'message' => 'Appointment booked successfully.',
                'data' => new AppointmentResource($appointment),
            ], 201);

        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * GET /api/receptionist/appointments/{id}
     * Show a single appointment.
     */
    public function show(Appointment $appointment): AppointmentResource
    {
        return new AppointmentResource($appointment->load(['patient', 'doctor']));
    }

    /**
     * PUT/PATCH /api/receptionist/appointments/{id}
     * Handle Rescheduling.
     */
    public function update(Request $request, Appointment $appointment): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
        ]);

        try {
            $updatedAppointment = $this->service->reschedule(
                $appointment,
                $validated['date'],
                $validated['time']
            );

            return response()->json([
                'message' => 'Appointment rescheduled successfully.',
                'data' => new AppointmentResource($updatedAppointment),
            ]);

        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * DELETE /api/receptionist/appointments/{id}
     * Standard delete (optional, if you allow hard deleting).
     */
    public function destroy(Appointment $appointment): JsonResponse
    {
        // If you prefer soft-deletes or cancelling via DELETE verb:
        $this->service->cancel($appointment);

        return response()->json(['message' => 'Appointment canceled.']);
    }

    /**
     * PATCH /api/receptionist/appointments/{id}/cancel
     * Custom Action: Cancel an appointment.
     */
    public function cancel(Appointment $appointment): JsonResponse
    {
        $this->service->cancel($appointment);

        return response()->json(['message' => 'Appointment canceled successfully.']);
    }

    /**
     * PATCH /api/receptionist/appointments/{id}/confirm
     * Custom Action: Confirm Check-In.
     */
    public function confirm(Appointment $appointment): JsonResponse
    {
        $this->service->reinstate($appointment);

        return response()->json(['message' => 'Check-in confirmed.']);
    }
}
