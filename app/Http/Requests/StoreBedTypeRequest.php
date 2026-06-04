<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBedTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-infrastructure');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'capacity' => 'required|integer',
        ];
    }
}
