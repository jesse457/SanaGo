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
    /**
     * Scope appointments to the current user's visibility.
     * Admin/finance sees all; doctors see only their own.
     */
    private function scopeAppointments($query)
    {
        $user = auth()->user();

        // Admin or finance roles see all appointments
        if ($user->can('view-finance') || $user->can('manage-users')) {
            return $query;
        }

        // Doctors see only their own appointments
        return $query->where('doctor_id', $user->id);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $parts = preg_split('/\s+/', trim($request->search));
        $appointments = Appointment::query()
            ->with(['patient', 'doctor'])
            ->tap(fn ($q) => $this->scopeAppointments($q))
            ->when($request->search, function ($query, $search) use ($parts) {
                $query->whereHas('patient', function ($q) use ($parts, $search) {
                    foreach ($parts as $part) {

                        $q->where('first_name', 'like', "%{$part}%")
                            ->orWhere('last_name', 'like', "%{$part}%");
                    }
                    $q->orWhere('patient_uid', 'like', "%{$search}%");
                });
            })
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->doctor_id, fn ($q, $id) => $q->where('doctor_id', $id))
            ->when($request->date, fn ($q, $date) => $q->whereDate('appointment_date', $date))
            ->orderBy('appointment_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Only show doctor filter dropdown to admin/reception
        $doctors = ($user->can('view-finance') || $user->can('manage-users'))
            ? User::all(['id', 'name'])
            : [];

        return Inertia::render('Appointments/Index', [
            'appointments' => $appointments,
            'doctors' => $doctors,
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

    public function calendar()
    {
        $appointments = Appointment::with(['patient', 'doctor'])
            ->tap(fn ($q) => $this->scopeAppointments($q))
            ->whereBetween('appointment_date', [now()->startOfMonth()->subDays(7), now()->endOfMonth()->addDays(7)])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        $statsBase = Appointment::query()->tap(fn ($q) => $this->scopeAppointments($q));

        $stats = [
            'today' => (clone $statsBase)->whereDate('appointment_date', today())->count(),
            'upcoming' => (clone $statsBase)->where('appointment_date', '>=', today())->where('status', '!=', 'completed')->count(),
            'completed' => (clone $statsBase)->where('status', 'completed')->count(),
            'cancelled' => (clone $statsBase)->where('status', 'cancelled')->count(),
        ];

        return Inertia::render('Appointments/Calendar', [
            'appointments' => $appointments,
            'stats' => $stats,
        ]);
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
