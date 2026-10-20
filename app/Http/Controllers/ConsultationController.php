<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConsultationRequest;
use App\Models\LabTestDefinition;
use App\Models\MedicalRecord;
use App\Models\Medication;
use App\Models\Patient;
use App\Services\Clinical\MedicalRecordService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class ConsultationController extends Controller
{
    public function __construct(
        private MedicalRecordService $medicalRecordService
    ) {}

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
        return Inertia::render('Consultations/Create', [
            'patients' => Patient::select([
                'id', 'first_name', 'last_name', 'patient_uid',
                'date_of_birth', 'gender', 'phone', 'allergies',
            ])->get(),

            'medications' => Medication::select([
                'id', 'name', 'unit_cost',
            ])
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),

            'labTests' => LabTestDefinition::select([
                'id', 'test_name', 'code',
            ])
                ->where('is_active', true)
                ->orderBy('test_name')
                ->get(),
        ]);
    }

    public function store(StoreConsultationRequest $request)
    {
        try {
            $attachments = $request->hasFile('attachments')
                ? $request->file('attachments')
                : null;

            $this->medicalRecordService->createConsultation(
                $request->validated(),
                $attachments
            );

            return redirect()
                ->route('consultations.create')
                ->with('success', 'Consultation saved successfully.');
        } catch (\Exception $e) {
            logger()->error('Consultation save failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'error' => 'An error occurred while saving. Please try again.',
            ])->withInput();
        }
    }

    /**
     * A consultation is a row of the patient's clinical record, so the detail
     * screen lives on the patient dossier rather than on a page of its own.
     */
    public function show(MedicalRecord $consultation): RedirectResponse
    {
        return redirect()->route('patients.show', $consultation->patient_id);
    }
}
