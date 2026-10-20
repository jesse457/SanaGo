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
        $labRequest = LabRequest::with(['patient', 'testDefinition'])
            ->findOrFail($request->get('request_id'));

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
