<?php

namespace App\Http\Requests\Dashboard;

use App\Http\Requests\AbstractApiRequest;

class CourseStoreRequest extends AbstractApiRequest
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
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:courses,code|max:255',
            'description' => 'nullable|string|max:500',
            'departments' => 'required|array|min:1',
            'departments.*' => 'required|integer|exists:departments,id',
            'semesters' => 'required|array|min:1',
            'semesters.*' => 'required|integer|exists:semesters,id',
            'teachers' => 'nullable|array|min:1',
            'teachers.*' => 'required|array|min:1',
            'teachers.*.*' => 'required|integer|exists:teachers,id',
        ];
    }
}
