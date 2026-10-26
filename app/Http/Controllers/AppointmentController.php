<?php

namespace App\Http\Controllers;

use App\Enums\AppointmentStatus;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use App\Services\PatientManagement\AppointmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    /**
     * Admin and reception see the whole diary; every other role (doctors)
     * only sees the appointments assigned to them.
     */
    private function scopeAppointments($query)
    {
        $user = auth()->user();

        if ($user->hasAnyRole(['admin', 'reception'])) {
            return $query;
        }

        return $query->where('doctor_id', $user->id);
    }

    private function canViewAllAppointments(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'reception']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $canViewAll = $this->canViewAllAppointments();
        $parts = preg_split('/\s+/', trim((string) $request->search));

        $appointments = Appointment::query()
            ->with(['patient', 'doctor.department'])
            ->tap(fn ($q) => $this->scopeAppointments($q))
            ->when($request->search, function ($query, $search) use ($parts) {
                // Grouped so the OR name/UID conditions stay inside the patient
                // match and cannot leak past the status/doctor/date filters.
                $query->whereHas('patient', function ($q) use ($parts, $search) {
                    $q->where(function ($nameQuery) use ($parts, $search) {
                        foreach ($parts as $part) {
                            $nameQuery->where('first_name', 'like', "%{$part}%")
                                ->orWhere('last_name', 'like', "%{$part}%");
                        }
                        $nameQuery->orWhere('patient_uid', 'like', "%{$search}%");
                    });
                });
            })
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->doctor_id, fn ($q, $id) => $q->where('doctor_id', $id))
            ->when($request->date, fn ($q, $date) => $q->where('appointment_date', $date))
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(10)
            ->withQueryString();

        // The calendar tab navigates client-side, so hand it the full (scoped)
        // diary once instead of round-tripping on every month change.
        $calendarAppointments = Appointment::query()
            ->with(['patient', 'doctor.department'])
            ->tap(fn ($q) => $this->scopeAppointments($q))
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        $today = today()->toDateString();

        $statsRow = Appointment::query()
            ->tap(fn ($q) => $this->scopeAppointments($q))
            ->selectRaw('count(*) as total')
            ->selectRaw('sum(case when status = ? then 1 else 0 end) as scheduled', [AppointmentStatus::SCHEDULED->value])
            ->selectRaw('sum(case when status = ? then 1 else 0 end) as completed', [AppointmentStatus::COMPLETED->value])
            ->selectRaw('sum(case when appointment_date = ? then 1 else 0 end) as today', [$today])
            ->selectRaw(
                'sum(case when appointment_date >= ? and status not in (?, ?) then 1 else 0 end) as upcoming',
                [$today, AppointmentStatus::COMPLETED->value, AppointmentStatus::CANCELLED->value],
            )
            ->selectRaw('sum(case when status = ? then 1 else 0 end) as cancelled', [AppointmentStatus::CANCELLED->value])
            ->first();

        $stats = [
            'total' => (int) ($statsRow->total ?? 0),
            'scheduled' => (int) ($statsRow->scheduled ?? 0),
            'completed' => (int) ($statsRow->completed ?? 0),
            'today' => (int) ($statsRow->today ?? 0),
            'upcoming' => (int) ($statsRow->upcoming ?? 0),
            'cancelled' => (int) ($statsRow->cancelled ?? 0),
        ];

        $view = $request->string('view')->toString() === 'calendar' ? 'calendar' : 'table';

        return Inertia::render('Appointments/Index', [
            'appointments' => $appointments,
            'calendarAppointments' => $calendarAppointments,
            'stats' => $stats,
            // Only admin/reception may filter by doctor — doctors are pinned to
            // their own diary and have no use for the dropdown.
            'doctors' => $canViewAll ? User::role('doctor')->get(['id', 'name']) : [],
            'filters' => [
                ...$request->only(['search', 'status', 'doctor_id', 'date']),
                'view' => $view,
            ],
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

            return redirect()->route('appointments.index')->with('success', 'Appointment booked successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * The calendar now lives inside the index page as a tab. Keep the old URL
     * working for bookmarks and dashboard links.
     */
    public function calendar(): RedirectResponse
    {
        return redirect()->route('appointments.index', ['view' => 'calendar']);
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
