<?php

namespace App\Http\Requests;

use App\Models\Department;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-departments');
    }

    public function rules(): array
    {
        // Get the department ID from the route (e.g., /departments/{department})
        // This will be null during a 'Store' action
        $departmentId = $this->route('department')?->id;

        return [
            'name' => 'required|string|max:255',

            // If updating, ignore the current department's ID for the unique check
            'code' => [
                'required',
                'string',
                Rule::unique('departments', 'code')->ignore($departmentId),
            ],

            'phone_extension' => 'required|string',
            'type' => 'required|in:clinical,administrative,support',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'head_of_department_id' => 'nullable|exists:users,id',
        ];
    }
}
