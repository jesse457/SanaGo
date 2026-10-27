<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
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
        $search = trim((string) $request->search);
        $parts = $search === '' ? [] : preg_split('/\s+/', $search);

        $patients = Patient::query()
            ->when($parts !== [], function ($query) use ($parts, $search) {
                // Group the OR chain: an ungrouped chain lets later filters
                // (or the uid match) escape the search constraint.
                $query->where(function ($inner) use ($parts, $search) {
                    foreach ($parts as $part) {
                        $inner->where('first_name', 'like', "%{$part}%")
                            ->orWhere('last_name', 'like', "%{$part}%");
                    }

                    $inner->orWhere('patient_uid', 'like', "%{$search}%");
                });
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
        // `patient_uid` is unique: a collision used to surface as a 500 on
        // registration. A ULID keeps it unique without a retry loop.
        $validated['patient_uid'] = 'PT-'.date('Y').'-'.strtoupper(substr((string) Str::ulid(), -6));

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

        // Registering at the cash desk must return to the desk with the new
        // patient selected, otherwise the cashier loses the queue position.
        if ($redirectTo === 'caisse') {
            return redirect()
                ->route('caisse.index', ['patient_id' => $patient->id])
                ->with('success', 'Patient registered. Collect payment to continue.');
        }

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

    /**
     * Patient dossier: identity, clinical history and financial summary.
     *
     * A patient used to be reachable only through list rows; five screens
     * link to `patients.show`, which had no implementation at all.
     *
     * History sections are optional props: the page shell renders immediately
     * and each tab requests its own section (eight rows at a time) on demand.
     */
    public function show(Patient $patient)
    {
        $patient->load([
            'latestAdmission.bed.ward',
            'latestAdmission.attendingDoctor:id,name',
        ]);

        $patient->loadCount([
            'admissions as admissions_count',
            'vitals as vitals_count',
            'medicalRecords as medical_records_count',
            'prescriptions as prescriptions_count',
            'appointments as appointments_count',
            'invoices as invoices_count',
            'labResults as lab_results_count',
        ]);

        $outstanding = (float) $patient->invoices()
            ->whereNotIn('status', [InvoiceStatus::PAID->value, InvoiceStatus::CANCELLED->value])
            ->sum('total_amount');

        $stats = [
            'admissions' => $patient->admissions_count,
            'vitals' => $patient->vitals_count,
            'consultations' => $patient->medical_records_count,
            'prescriptions' => $patient->prescriptions_count,
            'appointments' => $patient->appointments_count,
            'invoices' => $patient->invoices_count,
            'labResults' => $patient->lab_results_count,
            'outstanding' => $outstanding,
        ];

        $patient->makeHidden([
            'admissions_count',
            'vitals_count',
            'medical_records_count',
            'prescriptions_count',
            'appointments_count',
            'invoices_count',
            'lab_results_count',
        ]);

        return Inertia::render('Patients/Profile', [
            'patient' => $patient,
            'stats' => $stats,
            'admissions' => Inertia::optional(fn () => $patient->admissions()
                ->with(['bed.ward', 'attendingDoctor:id,name'])
                ->latest('admission_date')
                ->paginate(8, ['*'], 'admissions_page')),
            'vitals' => Inertia::optional(fn () => $patient->vitals()
                ->with('recordedBy:id,name')
                ->latest('recorded_at')
                ->paginate(8, ['*'], 'vitals_page')),
            'medicalRecords' => Inertia::optional(fn () => $patient->medicalRecords()
                ->with('doctor:id,name')
                ->latest('record_date')
                ->paginate(8, ['*'], 'consultations_page')),
            'prescriptions' => Inertia::optional(fn () => $patient->prescriptions()
                ->with('doctor:id,name')
                ->latest('prescription_date')
                ->paginate(8, ['*'], 'prescriptions_page')),
            'appointments' => Inertia::optional(fn () => $patient->appointments()
                ->with('doctor:id,name')
                ->latest('appointment_date')
                ->paginate(8, ['*'], 'appointments_page')),
            'invoices' => Inertia::optional(fn () => $patient->invoices()
                ->latest('invoice_date')
                ->paginate(8, ['*'], 'invoices_page')
                ->through(fn ($invoice) => $invoice->makeHidden([
                    'total_paid',
                    'remaining_balance',
                    'is_fully_paid',
                ]))),
            'labResults' => Inertia::optional(fn () => $patient->labResults()
                ->with(['labRequest.testDefinition', 'labRequest.doctor:id,name'])
                ->latest('result_date')
                ->paginate(8, ['*'], 'lab_results_page')),
        ]);
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
