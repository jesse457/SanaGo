<?php

namespace App\Http\Controllers;

use App\Enums\BedStatus;
use App\Http\Requests\StoreSurgeryRequest;
use App\Http\Requests\UpdateSurgeryRequest;
use App\Models\Bed;
use App\Models\Patient;
use App\Models\Surgery;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SurgeryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Surgeries/Index', [
            'surgeries' => Surgery::with(['patient', 'surgeon', 'anesthetist', 'bed'])
                ->orderBy('scheduled_at', 'desc')
                ->paginate(20),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Surgeries/Create', [
            'patients' => Patient::select('id', 'first_name', 'last_name', 'patient_uid', 'date_of_birth', 'gender')
                ->orderBy('last_name')
                ->get(),
            'doctors' => User::select('id', 'name')
                ->orderBy('name')
                ->get(),
            'beds' => Bed::with(['ward', 'bedType'])
                // Assuming AVAILABLE is the case name in BedStatus. Adjust if it is 'Available', 'Vacant', etc.
                ->where('status', BedStatus::Available)
                ->get()
                ->map(fn ($bed) => [
                    'id' => $bed->id,
                    'code' => $bed->code,
                    'ward_name' => $bed->ward?->name ?? 'N/A',
                    'type_name' => $bed->bedType?->name ?? 'N/A',
                ]),
        ]);
    }

    /**
     * Show the form for editing an existing surgery.
     */
    public function edit(Surgery $surgery): Response
    {
        return Inertia::render('Surgeries/Create', [
            'surgery' => $surgery->load(['patient', 'surgeon', 'anesthetist']),
            'patients' => Patient::select('id', 'first_name', 'last_name', 'patient_uid', 'date_of_birth', 'gender')
                ->orderBy('last_name')
                ->get(),
            'doctors' => User::select('id', 'name')
                ->orderBy('name')
                ->get(),
            // Fetch available beds, and include the bed currently assigned to this surgery
            'beds' => Bed::with(['ward', 'bedType'])
                ->where('status', BedStatus::Available)
                ->orWhere('id', $surgery->bed_id)
                ->get()
                ->map(fn ($bed) => [
                    'id' => $bed->id,
                    'code' => $bed->code,
                    'ward_name' => $bed->ward?->name ?? 'N/A',
                    'type_name' => $bed->bedType?->name ?? 'N/A',
                ]),
        ]);
    }

    public function store(StoreSurgeryRequest $request): RedirectResponse
    {
        Surgery::create($request->validated());

        return redirect()->route('surgeries.index')
            ->with('success', 'Surgery scheduled successfully.');
    }

    public function show(Surgery $surgery): Response
    {
        return Inertia::render('Surgeries/Show', [
            'surgery' => $surgery->load(['patient', 'surgeon', 'anesthetist', 'bed']),
        ]);
    }

    public function update(UpdateSurgeryRequest $request, Surgery $surgery): RedirectResponse
    {
        $surgery->update($request->validated());

        return redirect()->route('surgeries.index')
            ->with('success', 'Surgery updated successfully.');
    }

    public function destroy(Surgery $surgery): RedirectResponse
    {
        $surgery->delete();

        return redirect()->route('surgeries.index')
            ->with('success', 'Surgery cancelled.');
    }
}
