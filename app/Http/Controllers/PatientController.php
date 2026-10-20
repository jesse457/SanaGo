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
     */
    public function show(Patient $patient)
    {
        $patient->load([
            'latestAdmission.bed.ward',
            'admissions' => fn ($query) => $query->with('bed.ward')->latest('admission_date')->limit(10),
            'vitals' => fn ($query) => $query->with('recordedBy:id,name')->latest('recorded_at')->limit(20),
            'medicalRecords' => fn ($query) => $query->latest('record_date')->limit(10),
            'appointments' => fn ($query) => $query->latest('appointment_date')->limit(10),
            'invoices' => fn ($query) => $query->latest('invoice_date')->limit(10),
            'prescriptions' => fn ($query) => $query->latest()->limit(10),
        ]);

        return Inertia::render('Patients/Profile', [
            'patient' => $patient,
            'stats' => [
                'admissions' => $patient->admissions->count(),
                'vitals' => $patient->vitals->count(),
                'consultations' => $patient->medicalRecords->count(),
                'outstanding' => (float) $patient->invoices
                    ->whereNotIn('status', [InvoiceStatus::PAID->value, InvoiceStatus::CANCELLED->value])
                    ->sum('total_amount'),
            ],
            'admissions' => $patient->admissions,
            'vitals' => $patient->vitals,
            'medicalRecords' => $patient->medicalRecords,
            'appointments' => $patient->appointments,
            'invoices' => $patient->invoices,
            'prescriptions' => $patient->prescriptions,
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
