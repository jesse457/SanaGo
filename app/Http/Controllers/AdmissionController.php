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
            $query->whereHas('patient', function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                    ->orWhere('last_name', 'like', "%{$request->search}%")
                    ->orWhere('patient_uid', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return Inertia::render('Patients/Checkin', [
            'admissions' => $query->latest('admission_date')->paginate(10)->withQueryString(),
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
        return Inertia::render('Patients/Show', [
            'admission' => $admission->load([
                'patient',
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

    public function requestPatientAdmission(Patient $patient, AdmissionService $service)
    {
        $service->requestAdmission($patient, Auth::user());
    }

    public function create(Patient $patient)
    {
        return Inertia::render('Patients/Admission', [
            'patient' => $patient,
            'availableBeds' => Bed::with(['ward', 'bedType'])
                ->where('status', BedStatus::Available->value)
                ->get(),
            'doctors' => User::role('doctor')->get(['id', 'name']),
        ]);
    }

    public function store(StoreAdmissionRequest $request, AdmissionService $service)
    {
        try {
            $service->admit($request->validated());

            return redirect()->route('patients.show', $request->validated()['patient_id'])
                ->with('success', 'Patient admitted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Admission failed: '.$e->getMessage()]);
        }
    }
}
