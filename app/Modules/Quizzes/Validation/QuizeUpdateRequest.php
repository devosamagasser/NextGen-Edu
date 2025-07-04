<?php

namespace App\Modules\Quizzes\Validation;

use App\Rules\TimeRule;
use App\Http\Requests\AbstractApiRequest;
use App\Modules\Teachers\Rules\TeacherCourseDetailsRule;

class QuizeUpdateRequest extends AbstractApiRequest
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
            'description' => 'required|string',
            'total_degree' => 'required|integer',
            'date' => 'nullable|date|after_or_equal:today', 
            'start_time' => ['nullable','date_format:H:i:s', new TimeRule()],
            'duration' => 'required|integer|min:1',
            'question_degree' => 'required|integer|min:1',

            'new_questions' => 'nullable|array',
            'new_questions.*.question' => 'nullable|string',
            'new_questions.*.answers' => 'nullable|array|min:2',
            'new_questions.*.answers.*.answer' => 'nullable|string',
            'new_questions.*.answers.*.is_correct' => 'nullable|boolean',

            'old_questions' => 'nullable|array',
            'old_questions.*' => 'nullable|exists:questions,id',
        ];
    }
}
