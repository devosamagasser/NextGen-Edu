<?php

namespace App\Modules\Assignments\Validation;

use App\Rules\TimeRule;
use App\Http\Requests\AbstractApiRequest;
use App\Modules\Teachers\Rules\TeacherCourseDetailsRule;

class AssignmentStoreRequest extends AbstractApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('Teacher');
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_degree' => 'required|integer',
            'date' => 'required|date|after_or_equal:today', 
            'time' => ['required','date_format:H:i:s',new TimeRule()],
            'file' => 'required|file|mimes:pdf,doc,docx,csv,jpg,png,jfif,xlsx', // Adjust the file types and size as needed
        ];
    }
}
