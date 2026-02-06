<?php

namespace App\Http\Controllers\Api\Tenants\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminShiftService;
use Illuminate\Http\Request;

class AdminShiftController extends Controller
{
    protected $shiftService;

    public function __construct(AdminShiftService $shiftService)
    {
        $this->shiftService = $shiftService;
    }

    /**
     * GET /api/admin/shifts
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => $this->shiftService->getPaginatedShifts(15),
        ]);
    }

    /**
     * POST /api/admin/shifts
     */
    public function store(Request $request)
    {
        $data = $this->validateShift($request);

        $shift = $this->shiftService->saveShift($data);

        return response()->json([
            'success' => true,
            'message' => 'Shift created successfully',
            'data' => $shift
        ], 201);
    }

    /**
     * PUT/PATCH /api/admin/shifts/{id}
     */
    public function update(Request $request, $id)
    {
        $data = $this->validateShift($request);

        // Pass the ID to the service to trigger update logic
        $shift = $this->shiftService->saveShift($data, $id);

        return response()->json([
            'success' => true,
            'message' => 'Shift updated successfully',
            'data' => $shift
        ]);
    }

    /**
     * DELETE /api/admin/shifts/{id}
     */
    public function destroy($id)
    {
        $this->shiftService->deleteShift($id);

        return response()->json([
            'success' => true,
            'message' => 'Shift deleted successfully'
        ]);
    }

    /**
     * Helper to validate shift data
     */
    private function validateShift(Request $request): array
    {
        // Note: Removed spaces in 'in:' rule (e.g., 'Day,Night' instead of 'Day, Night')
        // to prevent validation failures on exact string matching.
        return $request->validate([
            'shift_type' => 'required|in:Day,Night,Morning,Evening',
            'shift_date' => 'required|date',
            'start_time' => 'required|date_format:H:i', // Enforce time format
            'end_time' => 'required|date_format:H:i',
        ]);
    }
}
