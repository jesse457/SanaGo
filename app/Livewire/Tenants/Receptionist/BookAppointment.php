<?php

namespace App\Livewire\Tenants\Receptionist;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use App\Traits\UserActivitiesTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
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

    public string $appointmentTime; // Represents scheduled arrival time (H:i)

    public ?string $reasonForVisit = null;

    public ?float $price = null;

    public array $doctors = [];

    public $foundPatients = [];

    // Updated validation rules
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
        $this->appointmentTime = now()->format('H:i'); // Default to current time for check-in

        // Cache doctors short term to reduce DB hits
        $this->doctors = Cache::remember('doctors_list_receptionist', 60, function () {
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
        });
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
                        // Single term or multiple fragments: match against indexed fields
                        foreach ($terms as $term) {
                            $q->orWhereBlind('first_name', 'first_name_index', $term)
                                ->orWhereBlind('last_name', 'last_name_index', $term)
                                ->orWhere('patient_uid', 'like', "%$term%");
                        }
                    }
                } catch (\Throwable $e) {
                    // Log error if blind index fails but continue without it
                    Log::warning('Blind index search failed: '.$e->getMessage());
                }
            })
                ->orWhere('patient_uid', 'ilike', "%{$term}%") // Add non-blind search for patient ID
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

    /**
     * Add a patient to the selected doctor's queue for the day.
     */
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
            DB::transaction(function () use ($doctor, $patient, $appointmentDateTime) {
                // Stricter check: Block if the patient has any non-terminal appointment
                // (Scheduled, Waiting, or In Consultation) for the same doctor/date.
                $isAlreadyActive = Appointment::query()
                    ->where('patient_id', $patient->id)
                    ->where('doctor_id', $doctor->id)
                    ->whereDate('appointment_date', $this->appointmentDate)
                    ->whereIn('status', ['Scheduled', 'Waiting', 'In Consultation'])
                    ->exists();

                if ($isAlreadyActive) {
                    throw new \RuntimeException('This patient already has an active appointment with this doctor on this date. Please check the appointment list or cancel the existing one.');
                }

                // FIX for PostgreSQL: Instead of locking the aggregate function,
                // we fetch the latest queue position row and lock it.
                $latestAppointment = Appointment::query()
                    ->where('doctor_id', $doctor->id)
                    ->whereDate('appointment_date', $this->appointmentDate)
                    ->orderByDesc('queue_position')
                    ->limit(1)
                    ->lockForUpdate() // This locks the specific latest row found
                    ->first();

                $lastPosition = $latestAppointment ? $latestAppointment->queue_position : 0;
                $nextPosition = $lastPosition + 1;

                // Create the appointment and add it to the queue
                $appointment = Appointment::create([
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'appointment_date' => $this->appointmentDate,
                    'appointment_time' => $appointmentDateTime,
                    'reason_for_visit' => $this->reasonForVisit,
                    'price' => $this->price,
                    'status' => 'Waiting', // Initial status is 'Waiting' (Checked In)
                    'queue_position' => $nextPosition,
                ]);

                $this->logActivity(
                    'appointment_waiting', // Changed from _queued to reflect 'Waiting' status
                    "Added patient {$patient->first_name} {$patient->last_name} to queue for Dr. {$doctor->name} at position #{$nextPosition} (Status: Waiting)",
                    ['patient_id' => $patient->id, 'doctor_id' => $doctor->id, 'appointment_id' => $appointment->id]
                );
            }, 5); // Retry 5 times on deadlock
        } catch (\RuntimeException $e) {
            Log::warning('Appointment booking blocked due to conflict: '.$e->getMessage());
            LivewireAlert::title('Conflict')->text($e->getMessage())->warning()->show();

            return;
        } catch (\Throwable $e) {
            Log::error('Queue booking failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            LivewireAlert::title('Server Error')->text('Unable to add patient to the queue. Please try again.')->error()->show();

            return;
        }

        LivewireAlert::title('Success')->success()->text("{$patient->first_name} has been added to the doctor's queue at position #{$nextPosition}.")->show();
        $this->resetForm();

        return redirect()->route('receptionist.appointments');
    }

    public function resetForm(): void
    {
        $this->reset();
        $this->mount();
    }

    public function render()
    {
        return view('livewire.tenants.receptionist.book-appointment');
    }
}
