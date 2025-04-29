<?php

namespace App\Modules\Table\Validation;

use Closure;
use App\Models\CourseDetail;
use Illuminate\Contracts\Validation\ValidationRule;

class IsDepartmentSemesterCorses implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $departmentId = request('department');
        $semesterId = request('semester');
        $courseExists = CourseDetail::where('course_id', $value)
            ->where('department_id', $departmentId)
            ->where('semester_id', $semesterId)
            ->exists();

        if (!$courseExists) {
            $fail('The selected course is not available for the specified department and semester.');
        }
    }
}
