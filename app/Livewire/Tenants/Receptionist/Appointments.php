<?php

namespace App\Livewire\Tenants\Receptionist;

use App\Models\Appointment;
use App\Models\User;
use App\Traits\UserActivitiesTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.receptionist')]
class Appointments extends Component
{
    use UserActivitiesTrait, WithPagination;

    // Filters (synced to URL)
    #[Url(as: 'date')]
    public ?string $dateFilter = null;

    #[Url(as: 'doctor')]
    public string $doctorFilter = '';

    #[Url(as: 'status')]
    public string $statusFilter = '';

    #[Url(as: 'search')]
    public string $patientSearch = '';

    // Data used to populate filter selects
    public Collection $doctors;

    // Reschedule modal state & fields
    public bool $showRescheduleModal = false;

    public ?int $rescheduleAppointmentId = null;

    public ?string $rescheduleDate = null;

    public ?string $rescheduleStart = null;

    /**
     * Validation rules for rescheduling an appointment.
     * The `end_time` field has been removed as per the updated Appointment model
     * which relies on `appointment_time` (scheduled time) and `actual_start_time`/`actual_end_time`.
     *
     * @var array
     */
    protected $rules = [
        'rescheduleDate' => 'required|date',
        'rescheduleStart' => 'required',
    ];

    /**
     * Component mount lifecycle hook.
     * Loads doctors for the filter dropdown.
     *
     * @return void
     */
    public function mount()
    {
        $this->doctors = User::where('role', 'doctor')->with('department')->get();
    }

    /**
     * Render the component view with filtered and paginated appointments.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $query = Appointment::query()
            ->with(['patient', 'doctor.department']);

        // Apply date filter
        if (! empty($this->dateFilter)) {
            $query->whereDate('appointment_date', $this->dateFilter);
        }

        // Apply doctor filter
        if (! empty($this->doctorFilter)) {
            $query->where('doctor_id', $this->doctorFilter);
        }

        // Apply status filter
        if (! empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        // Apply patient search filter
        if (! empty($this->patientSearch)) {
            // Split on whitespace, remove empty fragments
            $terms = array_filter(array_map('trim', preg_split('/\s+/', $this->patientSearch)));

            $query->whereHas('patient', function ($q) use ($terms) {
                // If exactly two terms, treat as first + last name
                if (count($terms) === 2) {
                    $first = $terms[0];
                    $last = $terms[1];

                    $q->whereBlind('first_name', 'first_name_index', $first)
                        ->whereBlind('last_name', 'last_name_index', $last);
                } else {
                    // For single or multiple fragments, match any fragment against first or last name.
                    $q->where(function ($sub) use ($terms) {
                        foreach ($terms as $term) {
                            // Use orWhereBlind so any fragment matches
                            $sub->orWhereBlind('first_name', 'first_name_index', $term)
                                ->orWhereBlind('last_name', 'last_name_index', $term);
                        }
                    });
                }
            });
        }

        // Order by most recent appointment date first, then time ascending
        // Note: 'appointment_time' is cast as a datetime, but we're relying on the database to handle time sorting.
        $query->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'asc');

        $appointments = $query->paginate(10);

        return view('livewire.tenants.receptionist.appointments', [
            'appointments' => $appointments,
        ]);
    }

    /**
     * Reset all filters and pagination.
     *
     * @return void
     */
    public function resetFilters()
    {
        $this->reset(['dateFilter', 'doctorFilter', 'statusFilter', 'patientSearch']);
        $this->resetPage();
    }

    /**
     * Reset pagination when any filter changes.
     *
     * @param  string  $propertyName
     * @return void
     */
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['dateFilter', 'doctorFilter', 'statusFilter', 'patientSearch'])) {
            $this->resetPage();
        }
    }

    /**
     * Open the reschedule modal and populate fields with appointment data.
     * The `rescheduleEnd` time field is no longer populated/used.
     *
     * @param  int  $appointmentId
     * @return void
     */
    public function openRescheduleModal($appointmentId)
    {
        $appointment = Appointment::find($appointmentId);

        if (! $appointment) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Appointment not found.']);

            return;
        }

        $this->rescheduleAppointmentId = $appointmentId;
        $this->rescheduleDate = Carbon::parse($appointment->appointment_date)->format('Y-m-d');
        // appointment_time is cast as datetime, so format it for H:i input
        $this->rescheduleStart = $appointment->appointment_time->format('H:i');

        $this->showRescheduleModal = true;
    }

    /**
     * Close the reschedule modal and reset related fields.
     *
     * @return void
     */
    public function closeRescheduleModal()
    {
        $this->showRescheduleModal = false;
        // Removed 'rescheduleEnd' from the reset list
        $this->reset(['rescheduleAppointmentId', 'rescheduleDate', 'rescheduleStart']);
        $this->resetValidation();
    }

    /**
     * Confirm and save the rescheduled appointment.
     * Status is set to 'Waiting' as it is the appropriate initial state in the new model.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function rescheduleAppointmentConfirm()
    {
        try {
            $this->validate();

            if (! $this->rescheduleAppointmentId) {
                throw ValidationException::withMessages(['rescheduleAppointmentId' => 'No appointment selected.']);
            }

            $appointment = Appointment::with('patient', 'doctor')->find($this->rescheduleAppointmentId);
            if (! $appointment) {
                throw ValidationException::withMessages(['rescheduleAppointmentId' => 'Appointment not found.']);
            }

            $latestAppointment = Appointment::query()
                ->where('doctor_id', $appointment->doctor_id)
                ->whereDate('appointment_date', $this->rescheduleDate)
                ->orderByDesc('queue_position')
                ->limit(1)
                ->lockForUpdate() // This locks the specific latest row found
                ->first();
            if ($latestAppointment && $latestAppointment->id === $appointment->id) {
                // If rescheduling to the same date, keep the same position
                $nextPosition = $appointment->queue_position;
            } else {
                $lastPosition = $latestAppointment ? $latestAppointment->queue_position : 0;
                $nextPosition = $lastPosition + 1;
            }

            // Save new values
            $appointment->appointment_date = $this->rescheduleDate;
            $appointment->appointment_time = $this->rescheduleStart;
            $appointment->queue_position = $nextPosition;
            // Removed end_time saving as it is not in the new Appointment model schema
            $appointment->status = 'Waiting'; // Set to 'Waiting' as the new initial state
            $appointment->save();
            $this->logActivity(
                'appointment_rescheduled',
                "Rescheduling appointment for patient {$appointment->patient->first_name} {$appointment->patient->last_name} with doctor {$appointment->doctor->name}",
                [
                    'patient_id' => $appointment->patient->id,
                    'doctor_id' => $appointment->doctor->id,
                    'appointment_id' => $appointment->id,
                ]
            );
            LivewireAlert::title('Success')->text('Appointment successfully rescheduled.')->success()->show();

            $this->closeRescheduleModal();
        } catch (ValidationException $ve) {
            throw $ve;
        } catch (\Throwable $e) {
            Log::error('Reschedule failed: '.$e->getMessage(), ['id' => $this->rescheduleAppointmentId]);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Failed to reschedule appointment. Please try again.']);
        }
    }

    /**
     * Confirms the intent to cancel an appointment via LivewireAlert confirmation.
     */
    public function confirmCancelAppointment(int $appointmentId): void
    {
        LivewireAlert::title('Cancel appointment')
            ->text('Are you sure you want to cancel this appointment? This action cannot be undone.')
            ->asConfirm()
            ->onConfirm('cancelAppointment', ['id' => $appointmentId])
            ->show();
    }

    /**
     * Cancel an appointment using the model helper.
     *
     * @param  array  $appointmentId
     * @return void
     */
    public function cancelAppointment($appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId['id']);
        if (! $appointment) {
            LivewireAlert::title('Error')->error()->text('Appointment not found.')->show();

            return;
        }

        $appointment->cancel(); // Uses the new model helper method

        $this->logActivity(
            'appointment_cancellation',
            'Cancellation for appointment ',
            [
                'appointment_id' => $appointmentId['id'],
            ]
        );
        LivewireAlert::title('Success')->text('Appointment canceled (Status: Canceled).')->success()->show();
    }

    /**
     * Confirms an appointment, setting its status to 'Waiting' (Checked-in).
     *
     * @param  int  $appointmentId
     * @return void
     */
    public function confirmAppointment($appointmentId)
    {
        $appointment = Appointment::find($appointmentId);
        if (! $appointment) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Appointment not found.']);

            return;
        }

        $appointment->status = 'Waiting'; // New status for a confirmed/checked-in patient
        $appointment->save();
        $this->logActivity(
            'appointment_confirmed_to_waiting',
            "Confirmation/Check-in for appointment ID {$appointmentId}. Status set to Waiting.",
            [
                'appointment_id' => $appointmentId,
            ]
        );
        LivewireAlert::title('Success')->text('Appointment confirmed. Status set to Waiting.')->success()->show();
    }

    /**
     * Reinstate a canceled appointment to 'Waiting' status.
     *
     * @param  int  $appointmentId
     * @return void
     */
    public function reinstateAppointment($appointmentId)
    {
        $appointment = Appointment::find($appointmentId);
        if (! $appointment) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Appointment not found.']);

            return;
        }

        $appointment->status = 'Waiting'; // Changed from 'Pending' to 'Waiting'
        $appointment->save();
        $this->logActivity(
            'appointment_reinstated',
            "Reinstated appointment ID {$appointmentId} to Waiting status",
            [
                'appointment_id' => $appointmentId,
            ]
        );
        LivewireAlert::title('Success')->text('Appointment reinstated to Waiting.')->success()->show();
    }

    /**
     * Starts the patient's consultation using the model helper.
     * Sets status to 'In Consultation' and logs actual_start_time.
     *
     * @return void
     */
    public function startConsultation(int $appointmentId)
    {
        $appointment = Appointment::find($appointmentId);
        if (! $appointment) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Appointment not found.']);

            return;
        }

        $appointment->startConsultation(); // Uses model helper

        $this->logActivity(
            'consultation_started',
            "Consultation started for appointment ID {$appointmentId}.",
            [
                'appointment_id' => $appointmentId,
            ]
        );
        LivewireAlert::title('Success')->text('Consultation started. Status: In Consultation.')->success()->show();
    }

    /**
     * Ends the patient's consultation using the model helper.
     * Sets status to 'Completed' and logs actual_end_time.
     *
     * @return void
     */
    public function endConsultation(int $appointmentId)
    {
        $appointment = Appointment::find($appointmentId);
        if (! $appointment) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Appointment not found.']);

            return;
        }

        $appointment->endConsultation(); // Uses model helper

        $this->logActivity(
            'consultation_completed',
            "Consultation completed for appointment ID {$appointmentId}.",
            [
                'appointment_id' => $appointmentId,
            ]
        );
        LivewireAlert::title('Success')->text('Consultation completed. Status: Completed.')->success()->show();
    }
}
