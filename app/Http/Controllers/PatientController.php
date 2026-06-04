<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $patients = Patient::query()
            ->when($request->search, function ($query, $search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('patient_uid', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        return Inertia::render('Patients/index', [
            'patients' => $patients,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Patients/Create');
    }

    public function store(StorePatientRequest $request)
    {
        $validated = $request->validated();
        $validated['patient_uid'] = 'PT-'.date('Y').'-'.strtoupper(Str::random(4));

        $patient = Patient::create($validated);

        if ($request->hasFile('photo')) {
            $patient->addMediaFromRequest('photo')->toMediaCollection('photo');
        }

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $patient->addMedia($file)->toMediaCollection('documents');
            }
        }

        return redirect()->route('patients.index')
            ->with('success', 'Patient registered successfully with ID: '.$validated['patient_uid']);
    }

    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        $patient->update($request->validated());

        if ($request->hasFile('photo')) {
            $patient->addMediaFromRequest('photo')->toMediaCollection('photo');
        }

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $patient->addMedia($file)->toMediaCollection('documents');
            }
        }

        return back()->with('success', 'Patient updated successfully');
    }
}
