<?php

namespace App\Http\Controllers\Api\Tenants\Receptionist;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdmissionResource;
use App\Http\Resources\PatientResource;
use App\Models\Admission;
use App\Models\Patient;
use App\Services\AdmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class AdmissionController extends Controller
{
    protected AdmissionService $service;

    public function __construct(AdmissionService $service)
    {
        $this->service = $service;
    }

    /**
     * GET /api/receptionist/beds/available
     * Returns a list of beds that are not occupied using the Service logic.
     */
    public function checkAvailability(): JsonResponse
    {
        // Use the service to get beds (keeps logic centralized)
        $beds = $this->service->getAvailableBeds()
            ->map(function ($bed) {
                return [
                    'id' => $bed->id,
                    // Handle case if your DB column is 'code' or 'bed_number'
                    'code' => $bed->code ?? $bed->bed_number,
                    'ward_name' => $bed->ward->name ?? 'General',
                    'type' => $bed->bedType->name ?? 'Standard',
                ];
            });

        return response()->json(['data' => $beds]);
    }

    /**
     * GET /api/receptionist/admissions
     */
    public function index(Request $request)
    {
        $search = $request->query('query', '');
        $patients = $this->service->getPatientsForCheckin($search, 15);

        return PatientResource::collection($patients);
    }

    /**
     * GET /api/receptionist/patients/{patient}/admissions
     */
    public function history(Patient $patient)
    {
        // Ensure the service method exists or use direct relation if simple
        $history = $this->service->getAdmissionsForPatient($patient);

        return AdmissionResource::collection($history);
    }

    /**
     * GET /api/receptionist/admissions/{admission}
     */
    public function show(Admission $admission)
    {
        return new AdmissionResource($admission->load(['patient', 'doctor', 'bed.ward']));
    }

    /**
     * POST /api/receptionist/admissions
     * Confirms a pending admission request.
     */
    public function store(Request $request)
    {
        // 1. Validate
        $validated = $request->validate([
            'admission_request_id' => 'required|exists:admissions,id', // The Pending Admission ID
            'bed_id' => 'required|exists:beds,id',
            'reason_for_admission' => 'required|string',
            'observation_fee' => 'nullable|numeric|min:0',
            'admission_date' => 'required|date',
        ]);

        try {
            // 2. Find the Pending Admission Model
            $admissionRequest = Admission::findOrFail($validated['admission_request_id']);

            // 3. Call the Service to perform logic (Bed checks, Transaction, Logging)
            // The service expects the Admission Model + Data Array
            $confirmedAdmission = $this->service->confirmAdmission($admissionRequest, $validated);

            return new AdmissionResource($confirmedAdmission);

        } catch (RuntimeException $e) {
            // Handle logical errors (e.g. Bed taken, Patient already admitted)
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred processing the admission.'], 500);
        }
    }

    /**
     * PATCH /api/receptionist/admissions/{id}/discharge
     * Marks an admitted patient as discharged and FREES THE BED.
     */
    public function discharge(Request $request, $id)
    {
        try {
            // Use $this->service (injected in constructor)
            $admission = $this->service->dischargePatient($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Patient discharged successfully.',
                'data' => new AdmissionResource($admission),
            ]);

        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An unexpected error occurred: '.$e->getMessage()], 500);
        }
    }
}
