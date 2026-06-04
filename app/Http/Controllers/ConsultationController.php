<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConsultationRequest;
use App\Models\LabTestDefinition;
use App\Models\MedicalRecord;
use App\Models\Medication;
use App\Models\Patient;
use App\Services\Clinical\MedicalRecordService;
use Inertia\Inertia;

class ConsultationController extends Controller
{
    public function patientProfile(Patient $patient)
    {
        return Inertia::render('Consultations/Index', [
            'patient' => $patient->load([
                'medicalRecords' => function ($query) {
                    $query->with('doctor:id,name')->latest('record_date');
                },
                'vitals' => function ($query) {
                    $query->with('recordedBy:id,name')->latest('recorded_at');
                },
            ])->append('age'), // Assuming you have an 'getAgeAttribute' in Patient model
        ]);
    }

    public function create()
    {
        // In a real large-scale app, you might want to fetch patients via an API route during search,
        // but passing them here matches your current UI structure.
        return Inertia::render('Consultations/Create', [
            'patients' => Patient::select('id', 'first_name', 'last_name', 'patient_uid', 'date_of_birth', 'gender', 'phone_number', 'allergies')->get(),
            'medications' => Medication::select('id', 'name', 'unit_price')->get(),
            'labTests' => LabTestDefinition::select('id', 'test_name', 'code')->get(),
        ]);
    }

    public function store(StoreConsultationRequest $request, MedicalRecordService $service)
    {
        try {
            $attachments = $request->hasFile('attachments') ? $request->file('attachments') : null;
            $service->createConsultation($request->validated(), $attachments);

            return redirect()->route('consultations.create')->with('success', 'Consultation saved successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'An error occurred: '.$e->getMessage()]);
        }
    }

    public function show(MedicalRecord $consultation)
    {

        return Inertia::render('Consultations/Detail', [
            'consultation' => $consultation->load(['patient', 'doctor']),
        ]);
    }
}
