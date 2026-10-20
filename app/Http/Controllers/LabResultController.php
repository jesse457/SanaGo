<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLabResultRequest;
use App\Http\Requests\UpdateLabResultRequest;
use App\Models\LabRequest;
use App\Models\LabResult;
use App\Services\Laboratory\LabResultService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LabResultController extends Controller
{
    public function index(Request $request)
    {
        $query = LabResult::with(['labRequest.patient', 'labRequest.testDefinition']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('labRequest.patient', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('patient_uid', 'like', "%{$search}%");
            })->orWhereHas('labRequest.testDefinition', function ($q) use ($search) {
                $q->where('test_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $results = $query->latest('result_date')->paginate(10)->withQueryString();

        return Inertia::render('LabTests/Results/Index', [
            'results' => $results,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(Request $request)
    {
        // Results are always entered for a specific request; opening the form
        // without one used to return a bare 404. Point the user back to the
        // request queue instead.
        $requestId = $request->get('request_id');

        if ($requestId === null) {
            return redirect()
                ->route('requests.index')
                ->with('error', 'Open a lab request and choose "enter results" to record a result.');
        }

        $labRequest = LabRequest::with(['patient', 'testDefinition'])
            ->findOrFail($requestId);

        return Inertia::render('LabTests/Results/Enter', [
            'labRequest' => $labRequest,
            'isEdit' => false,
        ]);
    }

    public function store(StoreLabResultRequest $request, LabResultService $service)
    {
        $attachments = $request->hasFile('attachments') ? $request->file('attachments') : null;
        $service->createResult($request->validated(), $attachments);

        return redirect()->route('results.index')->with('success', 'Lab result verified and saved.');
    }

    public function edit(LabResult $result)
    {
        $result->load(['labRequest.patient', 'labRequest.testDefinition']);

        return Inertia::render('LabTests/Results/Enter', [
            'labRequest' => $result->labRequest,
            'labResult' => $result,
            'isEdit' => true,
        ]);
    }

    public function update(UpdateLabResultRequest $request, LabResult $result, LabResultService $service)
    {
        $attachments = $request->hasFile('attachments') ? $request->file('attachments') : null;
        $service->updateResult($result, $request->validated(), $attachments);

        return redirect()->route('results.index')->with('success', 'Lab result updated successfully.');
    }

    public function destroy(LabResult $result)
    {
        $result->delete();

        return redirect()->back()->with('success', 'Result record removed.');
    }
}
