<?php

namespace App\Livewire\Tenants\Receptionist;

use App\Models\Appointment;
use App\Models\User;
use App\Services\AppointmentService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.receptionist')]
class Appointments extends Component
{
    use WithPagination; // Removed UserActivitiesTrait as the Service handles logging now

    #[Url(as: 'date')]
    public ?string $dateFilter = null;

    #[Url(as: 'doctor')]
    public string $doctorFilter = '';

    #[Url(as: 'status')]
    public string $statusFilter = '';

    #[Url(as: 'search')]
    public string $patientSearch = '';

    public Collection $doctors;

    // Reschedule Modal State
    public bool $showRescheduleModal = false;

    public ?int $rescheduleAppointmentId = null;

    public ?string $rescheduleDate = null;

    public ?string $rescheduleStart = null;

    protected $rules = [
        'rescheduleDate' => 'required|date',
        'rescheduleStart' => 'required',
    ];

    public function mount()
    {
        $this->doctors = User::where('role', 'doctor')->with('department')->get();
    }

    public function render(AppointmentService $service)
    {
        // 1. Prepare Filters for the Service
        $filters = [
            'date' => $this->dateFilter,
            'doctor_id' => $this->doctorFilter,
            'status' => $this->statusFilter,
            'search' => $this->patientSearch,
            // You can add 'sort_by' and 'sort_order' here if you add UI controls for them
        ];

        // 2. Get Paginated Results from Service
        $appointments = $service->getPaginatedAppointments($filters, 10);

        return view('livewire.tenants.receptionist.appointments', [
            'appointments' => $appointments,
        ]);
    }

    // --- Actions ---

    public function openRescheduleModal($appointmentId)
    {
        $appointment = Appointment::find($appointmentId);

        if (! $appointment) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Appointment not found.']);

            return;
        }

        $this->rescheduleAppointmentId = $appointmentId;
        // Format for HTML5 date/time inputs
        $this->rescheduleDate = Carbon::parse($appointment->appointment_date)->format('Y-m-d');
        $this->rescheduleStart = Carbon::parse($appointment->appointment_time)->format('H:i');
        $this->showRescheduleModal = true;
    }

    public function closeRescheduleModal()
    {
        $this->showRescheduleModal = false;
        $this->reset(['rescheduleAppointmentId', 'rescheduleDate', 'rescheduleStart']);
        $this->resetValidation();
    }

    public function rescheduleAppointmentConfirm(AppointmentService $service)
    {
        $this->validate();

        try {
            $appointment = Appointment::findOrFail($this->rescheduleAppointmentId);

            // Delegate logic and logging to Service
            $service->reschedule(
                $appointment,
                $this->rescheduleDate,
                $this->rescheduleStart
            );

            LivewireAlert::title('Success')->text('Appointment rescheduled successfully.')->success()->show();
            $this->closeRescheduleModal();

        } catch (\Throwable $e) {
            Log::error('Reschedule Error: '.$e->getMessage());
            LivewireAlert::title('Error')->text('Failed to reschedule appointment.')->error()->show();
        }
    }

    public function cancelAppointment($id, AppointmentService $service)
    {
        try {
            $appointment = Appointment::findOrFail($id);

            // Delegate logic and logging to Service
            $service->cancel($appointment);

            LivewireAlert::title('Success')->text('Appointment canceled.')->success()->show();

        } catch (\Throwable $e) {
            Log::error('Cancel Error: '.$e->getMessage());
            LivewireAlert::title('Error')->text('Failed to cancel appointment.')->error()->show();
        }
    }

    public function reinstateAppointment($id, AppointmentService $service)
    {
        try {
            $appointment = Appointment::findOrFail($id);

            // Delegate logic and logging to Service
            $service->reinstate($appointment);

            LivewireAlert::title('Success')->text('Appointment reinstated to Waiting status.')->success()->show();

        } catch (\Throwable $e) {
            Log::error('Reinstate Error: '.$e->getMessage());
            LivewireAlert::title('Error')->text('Failed to reinstate appointment.')->error()->show();
        }
    }

    // --- Helpers ---

    public function resetFilters()
    {
        $this->reset(['dateFilter', 'doctorFilter', 'statusFilter', 'patientSearch']);
        $this->resetPage();
    }

    public function updated($prop)
    {
        if (in_array($prop, ['dateFilter', 'doctorFilter', 'statusFilter', 'patientSearch'])) {
            $this->resetPage();
        }
    }
}
