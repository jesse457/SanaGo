<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferBedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-infrastructure');
    }

    public function rules(): array
    {
        return [
            'ward_id' => ['required', 'exists:wards,id'],
        ];
    }
}
