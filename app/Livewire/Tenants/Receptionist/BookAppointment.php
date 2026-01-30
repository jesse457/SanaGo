<?php

namespace App\Livewire\Tenants\Receptionist;

use App\Models\Patient;
use App\Models\User;
use App\Services\AppointmentService;
use App\Services\Dashboards\ReceptionistDashboardService;
use App\Services\PatientService;
use Illuminate\Database\Eloquent\Collection; // Make sure this is imported
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.receptionist')]
class BookAppointment extends Component
{
    // Fix 1: Use Collection instead of array for Eloquent results
    public array $doctors;

    public string $patientSearch = '';

    public ?int $selectedPatientId = null;

    public string $selectedPatientName = '';

    public ?int $doctorId = null;

    public string $appointmentDate;

    public string $appointmentTime;

    public ?string $reasonForVisit = null;

    public ?float $price = null;

    // Fix 2: Changed from Builder to Collection. Livewire cannot store a Builder.
    // We initialize it as an empty collection to avoid "uninitialized" errors.
    public Collection $foundPatients;

    public function mount(ReceptionistDashboardService $dashboardService): void
    {
        $this->appointmentDate = now()->toDateString();
        $this->appointmentTime = now()->format('H:i');

        // Initialize empty collection
        $this->foundPatients = new Collection;

        // Populate doctors
        $this->doctors = $dashboardService->getDoctorsList();
    }

    public function updatedPatientSearch(PatientService $service)
    {
        $this->selectedPatientId = null;
        $this->selectedPatientName = '';

        if (strlen($this->patientSearch) < 2) {
            $this->foundPatients = new Collection;

            return;
        }

        // Fix 3: Chain ->limit() and ->get() to execute the query
        // The service returns a Builder, we must convert it to a Collection here
        $this->foundPatients = $service->search($this->patientSearch)
            ->limit(20)
            ->get();
    }

    public function selectPatient($patientId, $patientName)
    {
        $this->selectedPatientId = $patientId;
        $this->selectedPatientName = $patientName;
        $this->patientSearch = $patientName;

        // Fix 4: clear using an empty Collection to match the property type
        $this->foundPatients = new Collection;
    }

    public function clearSelectedPatient()
    {
        $this->reset(['selectedPatientId', 'selectedPatientName', 'patientSearch']);
        $this->foundPatients = new Collection;
    }

    public function bookAppointment(AppointmentService $appointmentService)
    {
        $this->validate([
            'selectedPatientId' => 'required|exists:patients,id',
            'doctorId' => 'required|exists:users,id',
            'appointmentDate' => 'required|date|after_or_equal:today',
            'appointmentTime' => 'required',
            'price' => 'nullable|numeric|min:0',
        ]);

        try {
            $patient = Patient::findOrFail($this->selectedPatientId);
            $doctor = User::findOrFail($this->doctorId);

            $appointment = $appointmentService->createAppointment(
                $doctor,
                $patient,
                $this->appointmentDate,
                $this->appointmentTime,
                $this->reasonForVisit,
                $this->price
            );

            LivewireAlert::title('Success')
                ->success()
                ->text("Added to queue at #{$appointment->queue_position}")
                ->show();

            return redirect()->route('receptionist.appointments');

        } catch (\Throwable $e) {
            Log::error('Booking Error: '.$e->getMessage());
            LivewireAlert::title('Error')->text($e->getMessage())->error()->show();
        }
    }

    public function render()
    {
        return view('livewire.tenants.receptionist.book-appointment');
    }
}
