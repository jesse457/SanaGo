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

    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => $this->shiftService->getPaginatedShifts(15),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'shift_type' => 'required|in:Day, Night, Morning, Evening',
            'shift_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $shift = $this->shiftService->saveShift($data);

        return response()->json(['success' => true, 'message' => 'Shift created', 'data' => $shift]);
    }

    public function destroy($id)
    {
        $this->shiftService->deleteShift($id);

        return response()->json(['success' => true, 'message' => 'Shift deleted']);
    }
}
