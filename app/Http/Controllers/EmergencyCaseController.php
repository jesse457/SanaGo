<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmergencyCaseRequest;
use App\Http\Requests\UpdateEmergencyCaseRequest;
use App\Models\EmergencyCase;
use App\Models\Patient;
use Inertia\Inertia;
use Inertia\Response;

class EmergencyCaseController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Emergencies/Index', [
            'emergencies' => EmergencyCase::with(['patient', 'attendingDoctor', 'triageNurse'])
                ->orderBy('arrived_at', 'desc')
                ->paginate(20),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Emergencies/Create', [
            'patients' => Patient::select('id', 'first_name', 'last_name', 'patient_uid', 'date_of_birth', 'gender')
                ->orderBy('last_name')
                ->get(),
        ]);
    }

    public function store(StoreEmergencyCaseRequest $request): RedirectResponse
    {
        EmergencyCase::create($request->validated());

        return redirect()->route('emergencies.index')
            ->with('success', 'Emergency case recorded.');
    }

    public function show(EmergencyCase $emergencyCase): Response
    {
        return Inertia::render('Emergencies/Show', [
            'emergency' => $emergencyCase->load(['patient', 'attendingDoctor', 'triageNurse', 'admission']),
        ]);
    }

    public function edit(EmergencyCase $emergencyCase): Response
    {
        return Inertia::render('Emergencies/Edit', [
            'emergency' => $emergencyCase->load(['patient', 'attendingDoctor', 'triageNurse']),
        ]);
    }

    public function update(UpdateEmergencyCaseRequest $request, EmergencyCase $emergencyCase): RedirectResponse
    {
        $emergencyCase->update($request->validated());

        return redirect()->route('emergencies.index')
            ->with('success', 'Emergency case updated successfully.');
    }

    public function destroy(EmergencyCase $emergencyCase): RedirectResponse
    {
        $emergencyCase->delete();

        return redirect()->route('emergencies.index')
            ->with('success', 'Emergency case deleted.');
    }
}
