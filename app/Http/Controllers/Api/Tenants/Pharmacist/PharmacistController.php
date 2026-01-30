<?php

namespace App\Http\Controllers\Api\Tenants\Pharmacist;

use App\Http\Controllers\Controller;
use App\Models\Medication;
use App\Models\Prescription;
use App\Services\PharmacyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PharmacistController extends Controller
{
    protected PharmacyService $pharmacyService;

    public function __construct(PharmacyService $pharmacyService)
    {
        $this->pharmacyService = $pharmacyService;
    }

    /**
     * Get medications with filters.
     */
    public function getMedications(Request $request): JsonResponse
    {
        $search = $request->input('search', '');
        $statusFilter = $request->input('status', '');
        $perPage = $request->input('per_page', 10);

        $filters = [
            'search' => $search,
            'status' => $statusFilter,
        ];

        $medications = $this->pharmacyService->getInventoryQuery($filters)->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $medications,
        ]);
    }

    /**
     * Get a single medication by ID.
     */
    public function getMedication(Medication $medication): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $medication,
        ]);
    }

    /**
     * Create a new medication.
     */
    public function createMedication(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'dosage_unit' => 'required|string|max:50',
        ]);

        try {
            $medication = $this->pharmacyService->createMedication($validated);

            return response()->json([
                'success' => true,
                'message' => 'Medication created successfully',
                'data' => $medication,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update a medication.
     */
    public function updateMedication(Medication $medication, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'dosage_units' => 'nullable|string|max:100',
            'unit_price_purchase' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $updated = $this->pharmacyService->updateMedication($medication->id, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Medication updated successfully',
                'data' => $updated,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a medication.
     */
    public function deleteMedication(Medication $medication): JsonResponse
    {
        try {
            $this->pharmacyService->deleteMedication($medication->id);

            return response()->json([
                'success' => true,
                'message' => 'Medication deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get patients with search filter.
     */
    public function getPatients(Request $request): JsonResponse
    {
        $search = $request->input('search', '');
        $perPage = $request->input('per_page', 10);

        $patients = $this->pharmacyService->getPatientsQuery(['search' => $search])->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $patients,
        ]);
    }

    /**
     * Get prescriptions for a patient.
     */
    public function getPatientPrescriptions($patientId): JsonResponse
    {
        $prescriptions = Prescription::where('patient_id', $patientId)->latest()->with('doctor')->get();

        return response()->json([
            'success' => true,
            'data' => $prescriptions,
        ]);
    }

    /**
     * Get prescription with items and medications.
     */
    public function getPrescription($prescriptionId): JsonResponse
    {
        $prescription = Prescription::with(['items.medication', 'doctor'])->findOrFail($prescriptionId);

        // Calculate available to dispense quantities
        $prescription->items->each(function ($item) {
            $item->available_to_dispense = $item->quantity_prescribed - ($item->dispensed_quantity ?? 0);
        });

        return response()->json([
            'success' => true,
            'data' => $prescription,
        ]);
    }

    /**
     * Dispense prescription items.
     */
  /**
     * Dispense prescription items.
     */
    public function dispenseItems(Request $request, $prescriptionId): JsonResponse
    {
        // 1. Eager load items and medications to prevent N+1 queries downstream
        $prescription = Prescription::with(['items.medication'])
            ->findOrFail($prescriptionId);

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        try {
            // 2. Map items by ID for O(1) lookup instead of looping searching
            $prescriptionItems = $prescription->items->keyBy('id');
            $payload = [];

            foreach ($validated['items'] as $itemData) {
                // Instant lookup
                if (isset($prescriptionItems[$itemData['id']])) {
                    $payload[] = [
                        'model' => $prescriptionItems[$itemData['id']],
                        'quantity' => (int) $itemData['quantity'],
                        'notes' => $itemData['notes'] ?? '',
                    ];
                }
            }

            if (empty($payload)) {
                 return response()->json(['success' => false, 'message' => 'No valid items found'], 400);
            }

            $this->pharmacyService->dispenseItems($prescription, $payload, Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Items dispensed successfully',
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error("Dispense Error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500); // 500 implies server error, use 400 or 422 if it's a stock error logic
        }
    }
}
