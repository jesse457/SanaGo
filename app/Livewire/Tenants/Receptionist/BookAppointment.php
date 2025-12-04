<?php

namespace App\Livewire\Tenants\Receptionist;

use App\Models\Appointment; // Import Computed Attribute
use App\Models\Patient;
use App\Models\User;
use App\Traits\UserActivitiesTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.receptionist')]
class BookAppointment extends Component
{
    use UserActivitiesTrait;

    public string $patientSearch = '';

    public ?int $selectedPatientId = null;

    public string $selectedPatientName = '';

    public ?int $doctorId = null;

    public string $appointmentDate;

    public string $appointmentTime;

    public ?string $reasonForVisit = null;

    public ?float $price = null;

    // public array $doctors = []; // Removed: Converted to Computed Property for performance
    public $foundPatients = [];

    protected array $rules = [
        'selectedPatientId' => 'required|exists:patients,id',
        'doctorId' => 'required|exists:users,id',
        'appointmentDate' => 'required|date|after_or_equal:today',
        'appointmentTime' => 'required|date_format:H:i',
        'reasonForVisit' => 'nullable|string|max:500',
        'price' => 'nullable|numeric|min:0',
    ];

    public function mount(): void
    {
        $this->appointmentDate = now()->toDateString();
        $this->appointmentTime = now()->format('H:i');
    }

    /**
     * Computed Property: Doctors
     * This fixes the "Cold Start" and "No Redis" issue.
     * If Cache fails (Redis down), it catches the error and queries DB directly.
     */
    #[Computed]
    public function doctors()
    {
        // Define the query logic once to avoid duplication
        $fetchDoctors = function () {
            return User::query()
                ->where('role', 'doctor')
                ->select(['id', 'name', 'email', 'department_id'])
                ->with(['department:id,name'])
                ->orderBy('name')
                ->get()
                ->map(fn ($d) => [
                    'id' => $d->id,
                    'name' => $d->name,
                    'email' => $d->email,
                    'department' => $d->department?->name,
                ])->toArray();
        };

        try {
            // Attempt to retrieve from cache
            return Cache::remember('doctors_list_receptionist', 60, $fetchDoctors);
        } catch (\Exception $e) {
            // If Redis is down or connection refused, log it and fall back to DB
            Log::warning('Cache store unavailable, falling back to database: '.$e->getMessage());

            return $fetchDoctors();
        }
    }

    public function updatedPatientSearch()
    {
        $term = trim($this->patientSearch);
        $this->selectedPatientId = null;
        $this->selectedPatientName = '';

        if (strlen($term) >= 2) {
            $this->foundPatients = Patient::where(function ($q) use ($term) {
                try {
                    $terms = explode(' ', $term);
                    if (count($terms) === 2) {
                        $q->WhereBlind('first_name', 'first_name_index', $terms[0]);
                        $q->WhereBlind('last_name', 'last_name_index', $terms[1]);
                    } else {
                        foreach ($terms as $subTerm) {
                            $q->orWhereBlind('first_name', 'first_name_index', $subTerm)
                                ->orWhereBlind('last_name', 'last_name_index', $subTerm);
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning('Blind index search failed: '.$e->getMessage());
                }
            })
                ->limit(10)
                ->get();
        } else {
            $this->foundPatients = [];
        }
    }

    public function selectPatient($patientId, $patientName)
    {
        $this->selectedPatientId = $patientId;
        $this->selectedPatientName = $patientName;
        $this->patientSearch = $patientName;
        $this->foundPatients = [];
    }

    public function clearSelectedPatient()
    {
        $this->selectedPatientId = null;
        $this->selectedPatientName = '';
        $this->patientSearch = '';
        $this->foundPatients = [];
    }

    public function bookAppointment()
    {
        $this->validate();

        $patient = Patient::find($this->selectedPatientId);
        $doctor = User::find($this->doctorId);

        if (! $patient || ! $doctor) {
            LivewireAlert::title('Error')->text('Selected patient or doctor not found')->error()->show();

            return;
        }

        $appointmentDateTime = Carbon::createFromFormat('Y-m-d H:i', $this->appointmentDate.' '.$this->appointmentTime);
        $nextPosition = null;

        try {
            DB::transaction(function () use ($doctor, $patient, $appointmentDateTime, &$nextPosition) {

                // Block duplicate active appointments
                $isAlreadyActive = Appointment::query()
                    ->where('patient_id', $patient->id)
                    ->where('doctor_id', $doctor->id)
                    ->whereDate('appointment_date', $this->appointmentDate)
                    ->whereIn('status', ['Scheduled', 'Waiting', 'In Consultation'])
                    ->exists();

                if ($isAlreadyActive) {
                    throw new \RuntimeException('This patient already has an active appointment with this doctor on this date.');
                }

                // Lock the latest row to calculate queue position safely
                $latestAppointment = Appointment::query()
                    ->where('doctor_id', $doctor->id)
                    ->whereDate('appointment_date', $this->appointmentDate)
                    ->orderByDesc('queue_position')
                    ->limit(1)
                    ->lockForUpdate()
                    ->first();

                $lastPosition = $latestAppointment ? $latestAppointment->queue_position : 0;
                $nextPosition = $lastPosition + 1;

                $appointment = Appointment::create([
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'appointment_date' => $this->appointmentDate,
                    'appointment_time' => $appointmentDateTime,
                    'reason_for_visit' => $this->reasonForVisit,
                    'price' => $this->price,
                    'status' => 'Waiting',
                    'queue_position' => $nextPosition,
                ]);

                $this->logActivity(
                    'appointment_waiting',
                    "Added patient {$patient->first_name} {$patient->last_name} to queue for Dr. {$doctor->name} at position #{$nextPosition}",
                    ['patient_id' => $patient->id, 'doctor_id' => $doctor->id, 'appointment_id' => $appointment->id]
                );
            }, 5);
        } catch (\RuntimeException $e) {
            Log::warning('Booking blocked: '.$e->getMessage());
            LivewireAlert::title('Conflict')->text($e->getMessage())->warning()->show();

            return;
        } catch (\Throwable $e) {
            Log::error('Queue booking failed', ['error' => $e->getMessage()]);
            LivewireAlert::title('Server Error')->text('Unable to add patient to the queue.')->error()->show();

            return;
        }

        LivewireAlert::title('Success')->success()->text("{$patient->first_name} added to queue at position #{$nextPosition}.")->show();

        // No need to manually reset logic, simple redirect or reset
        $this->reset(['selectedPatientId', 'selectedPatientName', 'patientSearch', 'reasonForVisit', 'price', 'doctorId']);

        // If you prefer redirecting:
        return redirect()->route('receptionist.appointments');
    }

    public function render()
    {
        return view('livewire.tenants.receptionist.book-appointment', [
            'doctors' => $this->doctors(),
        ]);
    }
}
