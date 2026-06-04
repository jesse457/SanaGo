<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-users');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->route('user'))],
            'phone_number' => 'required|string|max:20',
            'role' => ['required', 'string', 'exists:roles,name'],
            'department_id' => 'nullable|exists:departments,id',
            'is_active' => 'boolean',
            'password' => 'nullable|string|min:8',
            'profile_picture' => 'nullable|image|max:2048',
            'shifts' => 'nullable|array',
            'shifts.*.shift_date' => 'required|date',
            'shifts.*.shift_type' => 'required|string|in:morning,afternoon,night',
            'shifts.*.start_time' => 'nullable',
            'shifts.*.end_time' => 'nullable',
        ];
    }
}
