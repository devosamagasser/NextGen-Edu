<?php

namespace App\Modules\Table\Validation;

use App\Http\Requests\AbstractApiRequest;

class TableStoreRequest extends AbstractApiRequest
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
            'type' => 'required|in:lecture,section,lab',
            'course_id' => 'required|exists:courses,id',
            'hall_id' => ['required','exists:halls,id', new EmptyHallRule()],
            'department_id' => 'required|exists:departments,id',
            'attendance' => 'required|in:online,offline',
            'day' => 'required|in:saturday,sunday,monday,tuesday,wednesday,thursday',
            'date' => 'required|date',
            'from' => 'required|date_format:H:i',
            'to' => 'required|date_format:H:i',
        ];
    }
}
