<?php

namespace App\Http\Requests;

use App\Enums\BedStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-infrastructure');
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', Rule::unique('beds', 'code')->ignore($this->route('bed'))],
            'status' => ['required', Rule::enum(BedStatus::class)],
            'ward_id' => 'required|exists:wards,id',
            'bed_type_id' => 'required|exists:bed_types,id',
        ];
    }
}
