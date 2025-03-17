<?php

namespace App\Modules\Announcments\Validation;

use App\Http\Requests\AbstractApiRequest;

class AnnouncementUpdateRequest extends AbstractApiRequest
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
            'department_id'  => ['required', 'integer', 'exists:departments,id'],
            'semester_id'    => ['required', 'integer', 'exists:semesters,id'],
            'course_id'      => ['required', 'integer', 'exists:courses,id'],
            'title'          => ['nullable', 'string', 'max:255'], // إضافة حد أقصى للطول
            'body'           => ['required', 'string'],
            'time_to_post'   => ['nullable', 'date', 'after_or_equal:today'], // التأكد من أن التاريخ في المستقبل أو اليوم
            'time'           => ['nullable', 'date_format:H:i'] 
        ];
    }
}
