<?php

namespace App\Http\Requests;

use App\Enums\AppointmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('edit-appointments');
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'string', Rule::enum(AppointmentStatus::class)],
            'appointment_date' => 'sometimes|date',
            'appointment_time' => 'sometimes',
        ];
    }
}
