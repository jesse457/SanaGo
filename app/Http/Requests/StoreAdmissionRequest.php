<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create-admissions');
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'bed_id' => 'required|exists:beds,id',
            'attending_doctor_id' => 'required|exists:users,id',
            'admission_date' => 'required|date',
            'reason_for_admission' => 'required|string|max:1000',
            'observation_fee' => 'nullable|numeric|min:0',
        ];
    }
}
