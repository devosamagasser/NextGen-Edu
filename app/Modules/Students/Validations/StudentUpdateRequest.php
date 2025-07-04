<?php

namespace App\Modules\Students\Validations;

use App\Http\Requests\AbstractApiRequest;

class StudentUpdateRequest extends AbstractApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('Admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|min:3',
            'department_id' => 'nullable|integer|exists:departments,id',
            'semester_id' => 'nullable|integer|exists:semesters,id',
            'nationality' => 'nullable|string|in:National,International',
            'personal_id' => "nullable|integer",
            'group' => 'nullable|integer|max:30',
        ];
    }
}
