<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserShiftRequest;
use App\Http\Requests\UpdateUserShiftRequest;
use App\Models\User;
use App\Models\UserShift;
use App\Services\System\ShiftService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UserShiftController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Shifts/Index', [
            'shifts' => UserShift::with('staffAssignments.user.department')
                ->orderBy('shift_date', 'desc')
                ->orderBy('shift_type')
                ->paginate(50),
            'users' => User::with('department:id,name')
                ->select('id', 'name', 'department_id')
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreUserShiftRequest $request, ShiftService $service): RedirectResponse
    {
        $service->create($request->validated());

        return redirect()->route('shifts.index')
            ->with('success', 'Shift created successfully.');
    }

    public function update(UpdateUserShiftRequest $request, UserShift $shift, ShiftService $service): RedirectResponse
    {
        $service->update($shift, $request->validated());

        return redirect()->route('shifts.index')
            ->with('success', 'Shift updated successfully.');
    }

    public function destroy(UserShift $shift, ShiftService $service): RedirectResponse
    {
        $service->delete($shift);

        return redirect()->route('shifts.index')
            ->with('success', 'Shift deleted successfully.');
    }
}
