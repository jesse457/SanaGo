<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserShiftRequest;
use App\Http\Requests\UpdateUserShiftRequest;
use App\Models\UserShift;
use App\Services\System\ShiftService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UserShiftController extends Controller
{
    public function index(ShiftService $service): Response
    {
        return Inertia::render('Shifts/Index', [
            'shifts' => UserShift::with('user.department')
                ->orderBy('shift_date', 'desc')
                ->orderBy('shift_type')
                ->paginate(50),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Shifts/Create');
    }

    public function store(StoreUserShiftRequest $request, ShiftService $service): RedirectResponse
    {
        $service->create($request->validated());

        return redirect()->route('shifts.index')
            ->with('success', 'Shift created successfully.');
    }

    public function edit(UserShift $shift): Response
    {
        return Inertia::render('Shifts/Edit', [
            'shift' => $shift->load('user'),
        ]);
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
