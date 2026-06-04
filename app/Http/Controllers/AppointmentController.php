<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use App\Services\PatientManagement\AppointmentService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $appointments = Appointment::query()
            ->with(['patient', 'doctor'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('patient_uid', 'like', "%{$search}%");
                });
            })
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->doctor_id, fn ($q, $id) => $q->where('doctor_id', $id))
            ->when($request->date, fn ($q, $date) => $q->whereDate('appointment_date', $date))
            ->orderBy('appointment_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Appointments/Index', [
            'appointments' => $appointments,
            'doctors' => User::all(['id', 'name']),
            'filters' => $request->only(['search', 'status', 'doctor_id', 'date']),
        ]);
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        $appointment->update($request->validated());

        return back()->with('success', 'Appointment updated successfully.');
    }

    public function store(StoreAppointmentRequest $request, AppointmentService $service)
    {
        try {
            $appointment = $service->book($request->validated());

            return redirect()->route('appointments.index')->with('success', "Booked! Queue Position: #{$appointment->queue_position}");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function create()
    {
        return Inertia::render('Appointments/Book', [
            'doctors' => User::role('doctor')
                ->with('department:id,name')
                ->get(['id', 'name', 'department_id']),
            'patients' => Patient::all(['id', 'first_name', 'last_name', 'patient_uid', 'phone']),
        ]);
    }
}
