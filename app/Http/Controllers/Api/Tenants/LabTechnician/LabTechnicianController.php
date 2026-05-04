<?php

namespace App\Http\Controllers\Api\Tenants\LabTechnician;

use App\Http\Controllers\Controller;
use App\Models\LabRequest;
use App\Models\LabResult;
use App\Models\LabTestDefinition;
use App\Services\LabService;
use App\Traits\LoggingTrait; // Imported Trait
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LabTechnicianController extends Controller
{
    use LoggingTrait; // Using Trait

    protected LabService $labService;

    public function __construct(LabService $labService)
    {
        $this->labService = $labService;
    }

    /**
     * Get lab requests with filters.
     */
    public function getLabRequests(Request $request): JsonResponse
    {
        try {
            $filters = [
                'search' => $request->input('search', ''),
                'status' => $request->input('status', ''),
            ];

            $perPage = $request->integer('per_page', 10);
            $requests = $this->labService->getLabRequestsQuery($filters)->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $requests,
            ]);
        } catch (\Exception $e) {
            $this->logException($e);
            return response()->json(['success' => false, 'message' => 'Failed to fetch lab requests.'], 500);
        }
    }

    /**
     * Start a lab request.
     */
    public function startRequest(LabRequest $labRequest): JsonResponse
    {
        try {
            // Validation: Only "Pending" requests can be started
            if ($labRequest->status !== 'Pending') {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot start request. Current status is: {$labRequest->status}",
                ], 422);
            }

            $this->labService->startRequest($labRequest);

            $this->logInfo("Lab request #{$labRequest->id} started by technician ID: " . Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Test request started successfully',
                'data' => $labRequest->refresh(),
            ]);
        } catch (\Exception $e) {
            $this->logException($e);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Create a new lab test definition.
     */
    public function createTestDefinition(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'test_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'units' => 'nullable|string|max:50',
            ]);

            $testDefinition = $this->labService->createTestDefinition($validated);

            $this->logInfo("New test definition created: {$testDefinition->test_name}");

            return response()->json([
                'success' => true,
                'message' => 'Test definition created successfully',
                'data' => $testDefinition,
            ], 201);

        } catch (ValidationException $e) {
            $this->logValidationError($e);
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            $this->logException($e);
            return response()->json(['success' => false, 'message' => 'Server Error'], 500);
        }
    }

    /**
     * Update a lab test definition.
     */
    public function updateTestDefinition(LabTestDefinition $testDefinition, Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'test_name' => 'required|string|max:255|unique:lab_test_definitions,test_name,' . $testDefinition->id,
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'units' => 'nullable|string|max:50',
            ]);

            $updated = $this->labService->updateTestDefinition($testDefinition, $validated);

            $this->logInfo("Test definition updated: ID {$testDefinition->id}");

            return response()->json([
                'success' => true,
                'message' => 'Test definition updated successfully',
                'data' => $updated,
            ]);
        } catch (ValidationException $e) {
            $this->logValidationError($e);
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            $this->logException($e);
            return response()->json(['success' => false, 'message' => 'Update failed'], 500);
        }
    }

    /**
     * Submit lab results.
     */
    public function submitResults(LabRequest $labRequest, Request $request): JsonResponse
    {
        try {
            // Check if results were already submitted
            if ($labRequest->status === 'Completed') {
                return response()->json(['success' => false, 'message' => 'Results have already been submitted for this request.'], 422);
            }

            $validated = $request->validate([
                'results_text' => 'required|string|min:5',
                'analysis_comments' => 'nullable|string',
                'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // Added mime types
            ]);

            $data = [
                'technician_id' => Auth::id(),
                'results_text' => $validated['results_text'],
                'analysis_comments' => $validated['analysis_comments'] ?? '',
            ];

            $attachments = $request->file('attachments', []);
            $this->labService->submitResults($labRequest, $data, $attachments);

            $this->logInfo("Results submitted for LabRequest #{$labRequest->id}");

            return response()->json([
                'success' => true,
                'message' => 'Lab results saved successfully',
                'data' => $labRequest->load('result'),
            ], 201);

        } catch (ValidationException $e) {
            $this->logValidationError($e);
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            $this->logException($e);
            return response()->json(['success' => false, 'message' => 'Submission failed'], 500);
        }
    }

    /**
     * Delete a lab test definition.
     */
    public function deleteTestDefinition(LabTestDefinition $testDefinition): JsonResponse
    {
        try {
            // Check if definition is used in any requests before deleting (Logic for Service)
            $this->labService->deleteTestDefinition($testDefinition);

            $this->logWarning("Test definition deleted: ID {$testDefinition->id} by user " . Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Test definition deleted successfully',
            ]);
        } catch (\Exception $e) {
            $this->logException($e);
            return response()->json(['success' => false, 'message' => 'Delete failed. It might be linked to existing records.'], 500);
        }
    }

    /**
     * Get lab results with filters.
     */
    public function getLabResults(Request $request): JsonResponse
    {
        try {
            $filters = [
                'search' => $request->input('search', ''),
                'date' => $request->input('date', ''),
            ];

            $results = $this->labService->getLabResultsQuery($filters)->paginate($request->integer('per_page', 10));

            return response()->json([
                'success' => true,
                'data' => $results,
            ]);
        } catch (\Exception $e) {
            $this->logException($e);
            return response()->json(['success' => false, 'message' => 'Error retrieving results'], 500);
        }
    }

    /**
     * Download lab result as PDF.
     */
    public function downloadResult(LabResult $labResult): JsonResponse
    {
        try {
            // Placeholder for PDF logic
            $this->logDebug("PDF Download initiated for Result ID: {$labResult->id}");

            return response()->json([
                'success' => true,
                'message' => 'PDF generation in progress',
                'data' => ['result_id' => $labResult->id],
            ]);
        } catch (\Exception $e) {
            $this->logException($e);
            return response()->json(['success' => false, 'message' => 'PDF generation failed'], 500);
        }
    }
}
