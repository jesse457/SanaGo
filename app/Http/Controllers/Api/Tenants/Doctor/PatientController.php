<?php

namespace App\Http\Controllers\Api\Tenants\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Resources\PatientResource;
use App\Services\PatientService;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    use HttpResponses;

    protected PatientService $patientService;

    public function __construct(PatientService $patientService)
    {
        $this->patientService = $patientService;
    }

    /**
     * Get a list of patients for the authenticated doctor.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $doctorId = Auth::id();
        $search = $request->get('search', '');
        $sortBy = $request->get('sort_by', 'last_name-asc');
        $perPage = $request->get('per_page', 15);

        $query = $this->patientService->getPatientsForDoctor($doctorId, $search);

        // Apply sorting
        $this->applySorting($query, $sortBy);

        $patients = $query->paginate($perPage);

        return PatientResource::collection($patients);
    }

    /**
     * Apply sorting to the patient query.
     */
    protected function applySorting($query, string $sortBy): void
    {
        $parts = explode('-', $sortBy);
        $field = $parts[0] ?? 'last_name';
        $direction = $parts[1] ?? 'asc';

        $column = match ($field) {
            'name' => 'last_name',
            'dob' => 'dob',
            default => 'last_name'
        };

        $query->orderBy($column, $direction);
    }
}
