<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use App\Models\UserActivity;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class AppointmentService
{
    /**
     * Private helper to log user activity
     */
    private function logActivity(string $type, string $description): void
    {
        UserActivity::create([
            'user_id' => Auth::id(),
            'activity_type' => $type, // 'created', 'updated', 'deleted'
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Build the query for fetching appointments with filters.
     */
    public function getAppointmentsQuery(array $filters): Builder
    {
        $query = Appointment::query()
            ->with(['patient', 'doctor.department']);

        if (! empty($filters['doctor_id'])) {
            $query->where('doctor_id', $filters['doctor_id']);
        }

        if (! empty($filters['status'])) {
            if (is_array($filters['status'])) {
                $query->whereIn('status', $filters['status']);
            } else {
                $query->where('status', $filters['status']);
            }
        }

        if (! empty($filters['date'])) {
            $query->whereDate('appointment_date', $filters['date']);
        }

        if (! empty($filters['date_range'])) {
            $query->whereBetween('appointment_date', $filters['date_range']);
        }

        if (! empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $terms = array_filter(array_map('trim', preg_split('/\s+/', $searchTerm)));

            $query->whereHas('patient', function ($q) use ($terms) {
                if (count($terms) === 2) {
                    $q->whereBlind('first_name', 'first_name_index', $terms[0])
                        ->whereBlind('last_name', 'last_name_index', $terms[1]);
                } else {
                    $q->where(function ($sub) use ($terms) {
                        foreach ($terms as $term) {
                            $sub->orWhereBlind('first_name', 'first_name_index', $term)
                                ->orWhereBlind('last_name', 'last_name_index', $term);
                        }
                    });
                }
            });
        }

        $sortBy = $filters['sort_by'] ?? 'appointment_time';
        $sortOrder = $filters['sort_order'] ?? 'asc';

        return $query->orderBy($sortBy, $sortOrder);
    }

    /**
     * Get paginated results.
     */
    public function getPaginatedAppointments(array $filters, int $perPage = 10)
    {
        return $this->getAppointmentsQuery($filters)->paginate($perPage);
    }

    /**
     * Get appointments for a specific doctor and date, grouped by Hour.
     */
    public function getDoctorDailyScheduleGrouped(int $doctorId, string $date): array
    {
        $appointments = $this->getAppointmentsQuery([
            'doctor_id' => $doctorId,
            'date' => $date,
            'sort_by' => 'appointment_time',
            'sort_order' => 'asc',
        ])
            ->orderBy('queue_position')
            ->get();

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

                $statusColor = match ($appointment->status) {
                    'In Consultation' => 'yellow',
                    'Completed' => 'green',
                    'Cancelled', 'Canceled' => 'red',
                    default => 'blue'
                };

                $patientsInSlot[] = [
                    'id' => $appointment->id,
                    'number' => $appointment->queue_position,
                    'raw_status' => $appointment->status,
                    'status_label' => __('doctor.status_'.Str::snake($appointment->status)),
                    'status_color' => $statusColor,
                    'patientName' => $patientName,
                    'patient_id' => $appointment->patient_id,
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
     * Get list of dates that have appointments for a doctor.
     */
    public function getDatesWithAppointments(int $doctorId): array
    {
        $startDate = now()->subDays(7)->startOfDay();
        $endDate = now()->addDays(20)->endOfDay();

        return Appointment::query()
            ->where('doctor_id', $doctorId)
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->whereIn('status', ['Scheduled', 'In Consultation', 'Pending', 'Waiting'])
            ->distinct()
            ->pluck('appointment_date')
            ->map(fn ($date) => Carbon::parse($date)->format('Y-m-d'))
            ->toArray();
    }

    /**
     * Start a consultation.
     */
    public function startConsultation(Appointment $appointment, int $doctorId): Appointment
    {
        if ($appointment->doctor_id !== $doctorId) {
            throw new Exception('Unauthorized access to this appointment.');
        }

        if (in_array($appointment->status, ['Completed', 'Cancelled'])) {
            throw new Exception("Cannot start a {$appointment->status} appointment.");
        }

        return DB::connection('pgsql_transaction')->transaction(function () use ($appointment) {
            $appointment->update(['status' => 'In Consultation']);

            $this->logActivity('updated', "Consultation started for Appointment #{$appointment->id} (Patient ID: {$appointment->patient_id})");

            return $appointment->refresh();
        });
    }

    /**
     * End a consultation.
     */
    public function endConsultation(Appointment $appointment, int $doctorId): Appointment
    {
        if ($appointment->doctor_id !== $doctorId) {
            throw new Exception('Unauthorized access to this appointment.');
        }

        if ($appointment->status !== 'In Consultation') {
            throw new Exception('Appointment is not currently in progress.');
        }

        return DB::connection('pgsql_transaction')->transaction(function () use ($appointment) {
            $appointment->update(['status' => 'Completed']);

            $this->logActivity('updated', "Consultation completed for Appointment #{$appointment->id}");

            return $appointment->refresh();
        });
    }

    /**
     * Create a new appointment with queue locking.
     */
    public function createAppointment(User $doctor, Patient $patient, string $date, string $time, ?string $reason = null): Appointment
    {
        $appointmentDateTime = Carbon::createFromFormat('Y-m-d H:i', $date.' '.$time);

        return DB::connection('pgsql_transaction')->transaction(function () use ($doctor, $patient, $date, $appointmentDateTime, $reason) {

            $exists = Appointment::where('patient_id', $patient->id)
                ->where('doctor_id', $doctor->id)
                ->whereDate('appointment_date', $date)
                ->whereIn('status', ['Waiting', 'In Consultation', 'Pending'])
                ->exists();

            if ($exists) {
                throw new RuntimeException('Patient already has an active appointment with this doctor today.');
            }

            $latest = Appointment::where('doctor_id', $doctor->id)
                ->whereDate('appointment_date', $date)
                ->orderByDesc('queue_position')
                ->lockForUpdate()
                ->first();

            $position = ($latest ? $latest->queue_position : 0) + 1;

            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'appointment_date' => $date,
                'appointment_time' => $appointmentDateTime,
                'reason_for_visit' => $reason,
                'status' => 'Waiting',
                'queue_position' => $position,
            ]);

            $this->logActivity('created', "New appointment created for Patient: {$patient->patient_uid} with Dr. {$doctor->name}");

            return $appointment;
        });
    }

    /**
     * Reschedule Logic.
     */
    public function reschedule(Appointment $appointment, string $newDate, string $newTime): Appointment
    {
        return DB::connection('pgsql_transaction')->transaction(function () use ($appointment, $newDate, $newTime) {
            $isSameDay = $appointment->appointment_date->format('Y-m-d') === $newDate;
            $nextPosition = $appointment->queue_position;

            if (! $isSameDay) {
                $latest = Appointment::where('doctor_id', $appointment->doctor_id)
                    ->whereDate('appointment_date', $newDate)
                    ->orderByDesc('queue_position')
                    ->lockForUpdate()
                    ->first();
                $nextPosition = ($latest ? $latest->queue_position : 0) + 1;
            }

            $appointment->update([
                'appointment_date' => $newDate,
                'appointment_time' => $newTime,
                'queue_position' => $nextPosition,
                'status' => 'Waiting',
            ]);

            $this->logActivity('updated', "Appointment #{$appointment->id} rescheduled to {$newDate} {$newTime}");

            return $appointment;
        });
    }

    public function cancel(Appointment $appointment): void
    {
        DB::connection('pgsql_transaction')->transaction(function () use ($appointment) {
            $appointment->update(['status' => 'Canceled']);
            $this->logActivity('updated', "Appointment #{$appointment->id} was canceled");
        });
    }

    public function reinstate(Appointment $appointment): void
    {
        DB::connection('pgsql_transaction')->transaction(function () use ($appointment) {
            $appointment->update(['status' => 'Waiting']);
            $this->logActivity('updated', "Appointment #{$appointment->id} was reinstated to Waiting status");
        });
    }
}
