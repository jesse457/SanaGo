<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLabResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create-lab-results');
    }

    public function rules(): array
    {
        return [
            'lab_request_id' => 'required|exists:lab_requests,id',
            'status' => 'required|string',
            'result_date' => 'required|date',
            'results_text' => 'required|string',
            'analysis_comments' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240',
        ];
    }
}
