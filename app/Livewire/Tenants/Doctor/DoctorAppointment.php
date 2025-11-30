<?php

namespace App\Livewire\Tenants\Doctor;

use App\Models\Appointment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

/**
 * DoctorAppointment Component
 * * Manages the daily timeline view of appointments for a doctor.
 * Allows viewing details, starting, and ending consultations.
 */
#[Layout('components.layouts.doctor')]
class DoctorAppointment extends Component
{
    /**
     * The currently selected date (Format: Y-m-d).
     */
    #[Rule('required|date')]
    public string $selectedDate;

    /**
     * Controls the visibility of the appointment details modal.
     */
    public bool $showModal = false;

    /**
     * Data passed to the modal when a time slot is clicked.
     */
    public array $modalGroupData = [];

    public function mount(): void
    {
        // Default to today
        $this->selectedDate = now()->toDateString();
    }

    /**
     * Fetches dates that have appointments to show indicators on the calendar strip.
     * Looks at a wider range (current week view).
     */
    #[Computed]
    public function datesWithAppointments(): array
    {
        $doctorId = Auth::id();

        // Look back 7 days and forward 14 days to cover scrolling range
        $startDate = now()->subDays(7)->startOfDay();
        $endDate = now()->addDays(14)->endOfDay();

        return Appointment::query()
            ->where('doctor_id', $doctorId)
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->whereIn('status', ['Scheduled', 'In Consultation', 'Pending']) // Optional: Filter active only
            ->distinct()
            ->pluck('appointment_date')
            ->map(fn ($date) => Carbon::parse($date)->format('Y-m-d'))
            ->toArray();
    }

    /**
     * Retrieves appointments for the selected date, grouped by the Hour.
     * Also prepares specific frontend data (colors, translations).
     */
    #[Computed]
    public function processedAppointments(): array
    {
        $this->validateOnly('selectedDate');

        $doctorId = Auth::id();

        $appointments = Appointment::query()
            ->where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $this->selectedDate)
            ->with('patient') // Eager load patient
            ->orderBy('appointment_time')
            ->orderBy('queue_position')
            ->get();

        // Group by Hour (00-23)
        $groupedByHour = $appointments->groupBy(function ($appointment) {
            return Carbon::parse($appointment->appointment_time)->format('H');
        });

        $processedGroups = [];

        foreach ($groupedByHour as $hour => $group) {
            $patientsInSlot = [];

            foreach ($group as $appointment) {
                $patientName = $appointment->patient
                    ? trim($appointment->patient->first_name.' '.$appointment->patient->last_name)
                    : 'Unknown Patient';

                // determine status color logic backend side for easier maintenance
                $statusColor = match ($appointment->status) {
                    'In Consultation' => 'yellow',
                    'Completed' => 'green',
                    'Cancelled' => 'red',
                    default => 'blue'
                };

                $patientsInSlot[] = [
                    'id' => $appointment->id,
                    'number' => $appointment->queue_position,
                    'raw_status' => $appointment->status,
                    'status_label' => __('doctor.status_'.Str::snake($appointment->status)), // Translate here
                    'status_color' => $statusColor,
                    'patientName' => $patientName,
                    'time' => Carbon::parse($appointment->appointment_time)->format('H:i'),
                    'type' => $appointment->reason_for_visit ?? 'General Checkup',
                    'notes' => $appointment->notes,
                ];
            }

            $formattedHour = sprintf('%02d', $hour);
            $timeSlotKey = "{$formattedHour}:00";

            $processedGroups[$timeSlotKey] = [
                'hourInt' => (int) $hour,
                'timeSlot' => $timeSlotKey,
                'hourRange' => "{$formattedHour}:00 - {$formattedHour}:59",
                'totalPatients' => $group->count(),
                'patients' => $patientsInSlot,
                'hasActive' => $group->contains('status', 'In Consultation'),
            ];
        }

        return $processedGroups;
    }

    /**
     * Open the modal for a specific hour block.
     */
    public function openGroupModal(string $timeSlot): void
    {
        $groups = $this->processedAppointments();

        if (isset($groups[$timeSlot])) {
            $this->modalGroupData = $groups[$timeSlot];
            $this->showModal = true;
        }
    }

    /**
     * Mark an appointment as 'In Consultation'.
     */
    public function startConsultation(int $appointmentId): void
    {
        $appointment = Appointment::findOrFail($appointmentId);

        // Security Check
        if ((int) $appointment->doctor_id !== (int) Auth::id()) {
            LivewireAlert::title('Unauthorized')->text('You cannot manage this appointment.')->error()->show();

            return;
        }

        // Update Logic
        $appointment->update(['status' => 'In Consultation']);

        // Refresh Modal Data immediately
        $timeSlotKey = Carbon::parse($appointment->appointment_time)->format('H:00');
        $this->refreshModalData($timeSlotKey);

        LivewireAlert::title('Started')
            ->text("Consultation started for {$appointment->patient->first_name}.")
            ->success()
            ->show();
    }

    /**
     * Mark appointment as completed or redirect to detailed form.
     */
    public function endConsultation(int $appointmentId): void
    {
        $appointment = Appointment::findOrFail($appointmentId);

        if ((int) $appointment->doctor_id !== (int) Auth::id()) {
            LivewireAlert::title('Unauthorized')->error()->show();

            return;
        }

        $appointment->update(['status' => 'Completed']);

        // Refresh Modal
        $timeSlotKey = Carbon::parse($appointment->appointment_time)->format('H:00');
        $this->refreshModalData($timeSlotKey);

        // Example: Redirect logic would go here
        // return $this->redirect(route('doctor.consultation.show', $appointmentId));

        LivewireAlert::title('Completed')->text('Consultation ended.')->success()->show();
    }

    /**
     * Helper to refresh modal data after status change without closing modal
     */
    private function refreshModalData(string $timeSlotKey): void
    {
        $groups = $this->processedAppointments();
        $this->modalGroupData = $groups[$timeSlotKey] ?? [];
    }

    public function render()
    {
        return view('livewire.tenants.doctor.doctor-appointment', [
            'appointmentGroups' => $this->processedAppointments(),
        ]);
    }
}
