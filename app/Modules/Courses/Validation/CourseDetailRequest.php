<?php

namespace App\Modules\Courses\Validation;

use App\Http\Requests\AbstractApiRequest;

class CourseDetailRequest extends AbstractApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('Super admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'details' => 'required|array|min:1',
            'details.*' => 'required|array',
            'details.*.department' => 'required|integer|exists:departments,id',
            'details.*.semester' => 'required|integer|exists:semesters,id',
            'details.*.teachers' => 'nullable|array',
            'details.*.teachers.*' => 'required|integer|exists:teachers,id',
        ];
    }
}
