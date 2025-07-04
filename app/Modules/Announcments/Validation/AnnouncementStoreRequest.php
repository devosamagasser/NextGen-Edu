<?php

namespace App\Modules\Announcments\Validation;

use App\Rules\TimeRule;
use App\Http\Requests\AbstractApiRequest;
use App\Modules\Teachers\Rules\InTeacherRelationRule;
use App\Modules\Teachers\Rules\TeacherCourseDetailsRule;

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
            'course_id' => ['required','exists:course_details,id',new TeacherCourseDetailsRule()],
            'title'     => ['nullable', 'string', 'max:255'], 
            'body'      => ['required', 'string'],
            'date'      => ['nullable', 'date', 'after_or_equal:today'], 
            'time'      => ['nullable', 'date_format:H:i', new TimeRule()] 
        ];
    }
}
