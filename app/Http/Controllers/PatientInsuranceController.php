<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientInsuranceRequest;
use App\Models\Patient;
use App\Models\PatientInsurance;
use App\Models\Payer;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PatientInsuranceController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Finance/PatientInsurances/Index', [
            'insurances' => PatientInsurance::with(['patient:id,first_name,last_name,patient_uid', 'payer:id,name,type'])
                ->orderBy('valid_until', 'desc')
                ->paginate(20),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Finance/PatientInsurances/Create', [
            'patients' => Patient::select('id', 'first_name', 'last_name', 'patient_uid', 'date_of_birth')
                ->orderBy('last_name')
                ->get(),
            'payers' => Payer::select('id', 'name', 'type')
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StorePatientInsuranceRequest $request): RedirectResponse
    {
        PatientInsurance::create($request->validated());

        return redirect()->route('patient-insurances.index')
            ->with('success', 'Patient insurance linked successfully.');
    }

    public function show(PatientInsurance $patientInsurance): Response
    {
        return Inertia::render('Finance/PatientInsurances/Show', [
            'insurance' => $patientInsurance->load([
                'patient:id,first_name,last_name,patient_uid,date_of_birth,gender,phone',
                'payer:id,name,type,coverage_percentage',
                'claims.invoice',
            ]),
        ]);
    }

    public function edit(PatientInsurance $patientInsurance): Response
    {
        return Inertia::render('Finance/PatientInsurances/Create', [
            'insurance' => $patientInsurance->load(['patient:id,first_name,last_name', 'payer:id,name']),
            'patients' => Patient::select('id', 'first_name', 'last_name', 'patient_uid', 'date_of_birth')
                ->orderBy('last_name')
                ->get(),
            'payers' => Payer::select('id', 'name', 'type')
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(StorePatientInsuranceRequest $request, PatientInsurance $patientInsurance): RedirectResponse
    {
        $patientInsurance->update($request->validated());

        return redirect()->route('patient-insurances.index')
            ->with('success', 'Patient insurance updated successfully.');
    }

    public function destroy(PatientInsurance $patientInsurance): RedirectResponse
    {
        $patientInsurance->delete();

        return redirect()->route('patient-insurances.index')
            ->with('success', 'Patient insurance removed.');
    }
}
