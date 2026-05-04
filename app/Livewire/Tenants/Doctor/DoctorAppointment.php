<?php

namespace App\Livewire\Tenants\Doctor;

use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

#[Layout('components.layouts.doctor')]
class DoctorAppointment extends Component
{
    #[Rule('required|date')]
    public string $selectedDate;

    public bool $showModal = false;

    public array $modalGroupData = [];

    // Dependency Injection works in mount() or action methods in Livewire
    protected AppointmentService $appointmentService;

    public function boot(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    public function mount(): void
    {
        $this->selectedDate = now()->toDateString();
    }

    #[Computed]
    public function datesWithAppointments(): array
    {
        return $this->appointmentService->getDatesWithAppointments(Auth::id());
    }

    #[Computed]
    public function processedAppointments(): array
    {
        $this->validateOnly('selectedDate');

        return $this->appointmentService->getDoctorDailyScheduleGrouped(
            Auth::id(),
            $this->selectedDate
        );
    }

    public function openGroupModal(string $timeSlot): void
    {
        $groups = $this->processedAppointments();
        if (isset($groups[$timeSlot])) {
            $this->modalGroupData = $groups[$timeSlot];
            $this->showModal = true;
        }
    }

    public function startConsultation(int $appointmentId): void
    {
        try {
            $appointment = Appointment::findOrFail($appointmentId);

            $this->appointmentService->startConsultation($appointment, Auth::id());

            // Refresh UI
            $this->refreshModalData($appointment->appointment_time->format('H:00'));

            LivewireAlert::title('Started')
                ->text("Consultation started for {$appointment->patient->first_name}.")
                ->success()
                ->show();

        } catch (\Exception $e) {
            LivewireAlert::title('Error')->text($e->getMessage())->error()->show();
        }
    }

    public function endConsultation(int $appointmentId): void
    {
        try {
            $appointment = Appointment::findOrFail($appointmentId);

            $this->appointmentService->endConsultation($appointment, Auth::id());

            // Refresh UI
            $this->refreshModalData($appointment->appointment_time->format('H:00'));

            LivewireAlert::title('Completed')->text('Consultation ended.')->success()->show();

        } catch (\Exception $e) {
            LivewireAlert::title('Error')->text($e->getMessage())->error()->show();
        }
    }

    private function refreshModalData(string $timeSlotKey): void
    {
        // Re-fetch the data from service to get updated statuses
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
