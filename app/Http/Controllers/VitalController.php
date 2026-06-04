<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVitalRequest;
use App\Http\Requests\UpdateVitalRequest;
use App\Models\Patient;
use App\Models\Vital;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class VitalController extends Controller
{
    public function create(Patient $patient)
    {
        return Inertia::render('Vitals/Create', [
            'patient' => [
                'id' => $patient->id,
                'first_name' => $patient->first_name,
                'last_name' => $patient->last_name,
                'patient_uid' => $patient->patient_uid,
            ],
        ]);
    }

    public function store(StoreVitalRequest $request)
    {
        $validated = $request->validated();
        $validated['recorded_by'] = Auth::id();

        Vital::create($validated);

        return redirect()->route('patients.show', $request->patient_id)
            ->with('success', 'Vitals recorded successfully.');
    }

    public function index(Patient $patient)
    {
        $vitals = $patient->vitals()
            ->with('recordedBy:id,name')
            ->latest('recorded_at')
            ->paginate(10);

        return Inertia::render('Vitals/Index', [
            'patient' => $patient,
            'vitals' => $vitals,
        ]);
    }

    public function update(UpdateVitalRequest $request, Vital $vital)
    {
        $vital->update($request->validated());

        return back()->with('success', 'Vitals record updated.');
    }

    public function destroy(Vital $vital)
    {
        $patientId = $vital->patient_id;
        $vital->delete();

        return redirect()->route('patients.show', $patientId)
            ->with('success', 'Vital record deleted.');
    }
}
