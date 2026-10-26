<?php

namespace App\Http\Requests;

use App\Enums\ShiftType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-users');
    }

    public function rules(): array
    {
        return [
            'staff' => ['nullable', 'array', 'max:50'],
            'staff.*.user_id' => ['nullable', 'integer', 'exists:users,id', 'required_without:staff.*.name'],
            'staff.*.name' => ['nullable', 'string', 'max:255', 'required_without:staff.*.user_id'],
            'shift_date' => ['required', 'date'],
            'shift_type' => ['required', Rule::enum(ShiftType::class)],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
        ];
    }
}
