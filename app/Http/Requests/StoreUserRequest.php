<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-users');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:20',
            'role' => ['required', 'string', 'exists:roles,name'],
            'department_id' => 'nullable|exists:departments,id',
            'is_active' => 'boolean',
            'password' => 'required|string|min:8',
            'profile_picture' => 'nullable|image|max:2048',
        ];
    }
}
