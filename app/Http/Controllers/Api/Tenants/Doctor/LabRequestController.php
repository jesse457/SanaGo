<?php

namespace App\Http\Controllers\Api\Tenants\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Resources\LabRequestResource;
use App\Services\LabService;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class LabRequestController extends Controller
{
    use HttpResponses;

    protected LabService $labService;

    public function __construct(LabService $labService)
    {
        $this->labService = $labService;
    }

    /**
     * Display a listing of lab requests for the authenticated doctor.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = [
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'doctor_id' => Auth::id(),
        ];

        $perPage = $request->get('per_page', 15);

        $query = $this->labService->getLabRequestsQuery($filters);

        $labRequests = $query->paginate($perPage);

        return LabRequestResource::collection($labRequests);
    }

    /**
     * Store a new lab request (optional, usually done via MedicalRecordApiController but good for standalone).
     */
    public function store(Request $request)
    {
        // For now, index is the main one requested by "create api for this component"
        // but we can add common methods.
    }
}
