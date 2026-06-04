<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDemoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public form — no authentication required
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:200'],
            'company' => ['nullable', 'string', 'max:200'],
            'message' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
