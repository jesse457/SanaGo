<?php

namespace App\Http\Controllers;

use App\Enums\AdmissionStatus;
use App\Enums\BedStatus;
use App\Http\Requests\StoreAdmissionRequest;
use App\Models\Admission;
use App\Models\Bed;
use App\Models\Patient;
use App\Models\User;
use App\Services\PatientManagement\AdmissionService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Admission::query()
            ->with(['patient:id,first_name,last_name,patient_uid', 'bed.ward', 'attendingDoctor:id,name']);

        if ($request->search) {
            $parts = preg_split('/\s+/', trim($request->search));

            // Grouped so the OR conditions cannot leak past the patient match
            // into the status filter applied below.
            $query->whereHas('patient', function ($q) use ($parts, $request) {
                $q->where(function ($nameQuery) use ($parts, $request) {
                    foreach ($parts as $part) {
                        $nameQuery->where('first_name', 'like', "%{$part}%")
                            ->orWhere('last_name', 'like', "%{$part}%");
                    }
                    $nameQuery->orWhere('patient_uid', 'like', "%{$request->search}%");
                });
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return Inertia::render('Patients/Checkin', [
            'admissions' => $query->latest()->paginate(10)->withQueryString(),
            'stats' => [
                'total' => Admission::count(),
                'active' => Admission::where('status', AdmissionStatus::Admitted->value)->count(),
                'pending' => Admission::where('status', AdmissionStatus::Pending->value)->count(),
                'discharged' => Admission::where('status', AdmissionStatus::Discharged->value)->count(),
            ],
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function show(Admission $admission)
    {
        // 1. Get the patient context
        $patient = $admission->patient;

        // 2. Fetch all historical admissions for this patient to populate the dropdown
        $admissions = Admission::where('patient_id', $patient->id)
            ->latest('admission_date')
            ->get()
            ->map(function ($record) {
                return [
                    'id' => $record->id,
                    'admission_date' => $record->admission_date ? Carbon::parse($record->admission_date)->format('M d, Y') : 'N/A',
                    'status' => $record->status,
                ];
            });

        // 3. Render page with loaded relations for the currently viewed admission record
        return Inertia::render('Patients/AdmissionDetails', [
            'patient' => $patient,
            'admissions' => $admissions,
            'selectedAdmission' => $admission->load([
                'bed.ward',
                'bed.bedType',
                'attendingDoctor:id,name',
            ]),
        ]);
    }

    public function discharge(Admission $admission, AdmissionService $service)
    {
        $service->discharge($admission);

        return redirect()->route('admissions.index')->with('success', 'Patient discharged and bed freed.');
    }

    public function requestPatientAdmission(Patient $patient, AdmissionService $service): RedirectResponse
    {
        $service->requestAdmission($patient, Auth::user());

        return back()->with('success', 'Admission request sent to nursing staff.');
    }

    public function create(Admission $admission)
    {
        // The admission form only renders identity fields; age derives from date_of_birth.
        $admission->load(['patient:id,first_name,last_name,patient_uid,date_of_birth,gender']);

        return Inertia::render('Patients/Admission', [
            'patient' => $admission->patient,
            'availableBeds' => Bed::query()
                ->with('ward:id,name')
                ->select('id', 'code', 'ward_id')
                ->where('status', BedStatus::Available->value)
                ->orderBy('code')
                ->get(),
            'doctors' => User::role('doctor')
                ->orderBy('name')
                ->get(['id', 'name']),
            'admission_id' => $admission->id,
        ]);
    }

    public function update(StoreAdmissionRequest $request, Admission $admission, AdmissionService $service)
    {
        try {
            $service->admit($request->validated(), $admission);

            return redirect()->route('patients.show', $request->validated()['patient_id'])
                ->with('success', 'Patient admitted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Admission failed: '.$e->getMessage()]);
        }
    }
}
