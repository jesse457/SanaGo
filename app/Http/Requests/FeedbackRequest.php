<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject' => 'required|string|max:255',
            'category' => 'required|in:dashboard,shift-management,revenue-report,settings,other',
            'message' => 'required|string|min:10',
        ];
    }
}
