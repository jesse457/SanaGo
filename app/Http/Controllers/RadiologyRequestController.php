<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRadiologyRequestRequest;
use App\Models\Patient;
use App\Models\RadiologyRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RadiologyRequestController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Radiology/Requests/Index', [
            'requests' => RadiologyRequest::with(['patient:id,first_name,last_name,patient_uid', 'requestedBy:id,name', 'result'])
                ->orderBy('created_at', 'desc')
                ->paginate(20),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Radiology/Requests/Create', [
            'patients' => Patient::select('id', 'first_name', 'last_name', 'patient_uid', 'date_of_birth', 'gender')
                ->orderBy('last_name')
                ->get(),
        ]);
    }

    public function store(StoreRadiologyRequestRequest $request): RedirectResponse
    {
        RadiologyRequest::create([
            ...$request->validated(),
            'requested_by' => auth()->id(),
            'status' => 'pending',
        ]);

        return redirect()->route('radiology.requests.index')
            ->with('success', 'Radiology request created successfully.');
    }

    public function show(RadiologyRequest $radiologyRequest): Response
    {
        return Inertia::render('Radiology/Requests/Show', [
            'request' => $radiologyRequest->load([
                'patient:id,first_name,last_name,patient_uid,date_of_birth,gender',
                'requestedBy:id,name',
                'result.performedBy:id,name',
                'result.validatedBy:id,name',
            ]),
        ]);
    }

    public function destroy(RadiologyRequest $radiologyRequest): RedirectResponse
    {
        $radiologyRequest->delete();

        return redirect()->route('radiology.requests.index')
            ->with('success', 'Radiology request cancelled.');
    }

    public function print(RadiologyRequest $radiologyRequest): Response
    {
        return Inertia::render('Radiology/Requests/Print', [
            'request' => $radiologyRequest->load(['patient', 'requestedBy']),
        ]);
    }
}
