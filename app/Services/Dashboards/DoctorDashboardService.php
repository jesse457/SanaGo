<?php

namespace App\Services\Dashboards;

use App\Models\Appointment;
use App\Models\LabResult;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class DoctorDashboardService
{
    /**
     * Get all dashboard data in one array (Useful for API response).
     */
    public function getDashboardData(int $doctorId): array
    {
        return [
            'upcoming_appointments' => $this->getUpcomingAppointments($doctorId),
            'patients_under_care' => $this->getPatientsUnderCare($doctorId),
            'incoming_lab_results' => $this->getIncomingLabResults($doctorId),
        ];
    }

    /**
     * Load upcoming appointments for the specific doctor.
     */
    public function getUpcomingAppointments(int $doctorId): Collection
    {
        return Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', '>=', Carbon::today())
            ->with('patient') // Ensure Patient model exists and relationship is defined
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();
    }

    /**
     * Load patients linked to admissions with this doctor.
     */
    public function getPatientsUnderCare(int $doctorId): Collection
    {
        return Patient::whereHas('admissions', function ($query) use ($doctorId) {
            $query->where('doctor_id', $doctorId);
        })
            ->distinct()
            ->get();
    }

    /**
     * Load recent lab results intended for this doctor.
     */
    public function getIncomingLabResults(int $doctorId): Collection
    {
        return LabResult::where('doctor_id', $doctorId)
            ->with(['labRequest.patient', 'labRequest.testDefinition'])
            ->where('result_date', '>=', Carbon::today())
            ->orderBy('result_date', 'desc')
            ->limit(5)
            ->get();
    }
}
