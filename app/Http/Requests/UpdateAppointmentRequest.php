<?php

namespace App\Http\Requests;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if ($user->can('edit-appointments')) {
            return true;
        }

        $appointment = $this->route('appointment');

        // Doctors may move their own appointments through the queue even
        // without the global edit-appointments permission.
        return $user->hasRole('doctor')
            && $appointment instanceof Appointment
            && $appointment->doctor_id === $user->id;
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'string', Rule::enum(AppointmentStatus::class)],
            'appointment_date' => ['sometimes', 'date'],
            'appointment_time' => ['sometimes'],
        ];
    }
}
