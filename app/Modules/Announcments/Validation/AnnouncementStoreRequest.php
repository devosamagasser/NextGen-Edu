<?php

namespace App\Modules\Announcments\Validation;

use App\Http\Requests\AbstractApiRequest;
use App\Modules\Teachers\Rules\InTeacherRelationRule;

class AnnouncementStoreRequest extends AbstractApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasAnyRole(['Admin', 'Teacher']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'department_id'  => ['required', 'integer', 'exists:departments,id', new InTeacherRelationRule('departments')],
            'semester_id'    => ['required', 'integer', 'exists:semesters,id', new InTeacherRelationRule('semesters')],
            'course_id'      => ['required', 'integer', 'exists:courses,id', new InTeacherRelationRule('courses')],
            'title'          => ['nullable', 'string', 'max:255'], 
            'body'           => ['required', 'string'],
            'time_to_post'   => ['nullable', 'date', 'after_or_equal:today'], 
            'time'           => ['nullable', 'date_format:H:i'] 
        ];
    }
}
