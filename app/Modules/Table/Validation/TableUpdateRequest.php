<?php

namespace App\Modules\Table\Validation;

use App\Http\Requests\AbstractApiRequest;

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
        return [
            'type' => 'nullable|in:lecture,section,lab',
            'course_id' => 'nullable|exists:courses,id',
            'department_id' => 'nullable|exists:departments,id',
            'attendance' => 'nullable|in:online,offline',
            'day' => 'nullable|in:saturday,sunday,monday,tuesday,wednesday,thursday',
            'date' => 'nullable|date',
            'from' => 'nullable|date_format:H:i',
            'to' => 'nullable|date_format:H:i',
            'hall_id' => [
                'nullable',
                'exists:halls,id',
                 new EmptyHallRule($this->route('table'))
                ],
            'weak' => 'nullable|integer',
            'status' => 'nullable|in:in time,started,finished,postponed',
        ];
    }
}
