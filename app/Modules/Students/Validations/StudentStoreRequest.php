<?php

namespace App\Modules\Students\Validations;

use App\Http\Requests\AbstractApiRequest;

class StudentStoreRequest extends AbstractApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasAnyRole('Admin','Super admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3',
            'department_id' => 'required|integer|exists:departments,id',
            'semester_id' => 'required|integer|exists:semesters,id',
            'nationality' => 'required|string|in:National,International',
            'personal_id' => 'required|integer|unique:students,personal_id',
        ];
    }
}
