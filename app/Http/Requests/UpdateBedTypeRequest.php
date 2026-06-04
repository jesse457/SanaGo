<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBedTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-infrastructure');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'capacity' => 'required|integer',
            'description' => 'nullable|string',
        ];
    }
}
