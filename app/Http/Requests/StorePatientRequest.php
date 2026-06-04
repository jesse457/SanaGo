<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create-patients');
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|unique:patients,email',
            'phone' => 'nullable|string|max:30',
            'date_of_birth' => 'nullable|date',
            'gender' => ['nullable', Rule::enum(Gender::class)],
            'address' => 'nullable|string',
            'blood_type' => 'nullable|string|max:10',
            'allergies' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'documents' => 'nullable|array',
            'documents.*' => 'file|max:10240',
        ];
    }
}
