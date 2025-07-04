<?php

namespace App\Modules\Table\Validation;

use App\Http\Requests\AbstractApiRequest;
use App\Modules\Departments\Department;

class TableUpdateRequest extends AbstractApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole(['Super admin', 'Admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $department = request()->route('department');
        $semester = request()->route('semeter');

        return [
            'type' => 'required|array|min:1',
            'type.*' => 'required|in:lecture,section,lab',
            'course_id' => 'required|array|min:1',
            'course_id.*' => ['required','exists:courses,id', new IsDepartmentSemesterCorses()],
            'hall_id' => ['required','array','min:1'],
            'hall_id.*' => ['required','exists:halls,id', new EmptyHallRule($department, $semester)],
            'attendance' => 'required|array|min:1',
            'attendance.*' => 'required|in:online,offline',
            'day' => 'required|array|min:1',
            'day.*' => 'required|in:saturday,sunday,monday,tuesday,wednesday,thursday',
            'date' => 'nullable|array|min:1',
            'date.*' => 'nullable|date',
            'from' => 'required|array|min:1',
            'from.*' => 'required|date_format:H:i:',
            'to' => 'required|array|min:1',
            'to.*' => 'required|date_format:H:i',
        ];
    }
}
