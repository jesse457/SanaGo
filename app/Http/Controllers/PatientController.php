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
        $parts = preg_split('/\s+/', trim($request->search));
        $patients = Patient::query()
            ->when($request->search, function ($query, $search) use ($parts) {
                foreach ($parts as $part) {
                    // dump($part);
                    $query->where('first_name', 'like', "%{$part}%")
                        ->orWhere('last_name', 'like', "%{$part}%");
                }
                $query->orWhere('patient_uid', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(12) // Limits to 12 patients per page
            ->withQueryString();

        return Inertia::render('Patients/Index', [
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

        // Create the patient
        $patient = Patient::create($validated);

        // Handle media uploads if present
        if ($request->hasFile('photo')) {
            $patient->addMediaFromRequest('photo')->toMediaCollection('photo');
        }

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $patient->addMedia($file)->toMediaCollection('documents');
            }
        }

        // Check redirect choice from either the request body or query parameters
        $redirectTo = $request->input('redirect_to') ?? $request->query('redirect_to');

        if ($redirectTo === 'vitals') {
            return redirect()
                ->route('vitals.create', $patient->id)
                ->with('success', 'Patient registered successfully. Please record their vitals.');
        }

        // Default redirect to the patients list
        // (Note: index routes typically do not require an ID parameter)
        return redirect()
            ->route('patients.index')
            ->with('success', 'Patient registered successfully.');
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
