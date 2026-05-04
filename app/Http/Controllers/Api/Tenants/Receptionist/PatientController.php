<?php

namespace App\Http\Controllers\Api\Tenants\Receptionist;

use App\Http\Controllers\Controller;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use App\Services\PatientService;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    protected $service;

    public function __construct(PatientService $service)
    {
        $this->service = $service;
    }

    /**
     * GET /api/receptionist/patients
     */
    public function index(Request $request)
    {
        // $this->service->search() returns a Builder, we paginate it here
        $patients = $this->service->search($request->query('query', ''))
            ->paginate($request->query('per_page', 15));

        return PatientResource::collection($patients);
    }

    /**
     * POST /api/receptionist/patients
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'gender' => 'required|in:male,female,other',
            'age' => 'nullable|integer',
            'address' => 'nullable|string',
        ]);

        $patient = $this->service->createPatient($validated);

        return new PatientResource($patient);
    }

    /**
     * GET /api/receptionist/patients/{id}
     */
    public function show(Patient $patient)
    {
        // Eager load relationships for the profile view
        return new PatientResource($patient->loadCount('appointments'));
    }

    /**
     * PUT /api/receptionist/patients/{id}
     */
    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string',
            // ... add other fields
        ]);

        $updatedPatient = $this->service->updatePatient($patient, $validated);

        return new PatientResource($updatedPatient);
    }

    /**
     * DELETE /api/receptionist/patients/{id}
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();

        return response()->json(['message' => 'Patient deleted successfully']);
    }

    /**
     * GET /api/receptionist/patients/search
     * (Your custom logic for the dropdown in Book Appointment)
     */
    public function search(Request $request)
    {
        $results = $this->service->search($request->query('query', ''))->limit(10)->get();

        return PatientResource::collection($results);
    }
}
