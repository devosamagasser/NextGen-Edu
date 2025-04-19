<?php

namespace App\Modules\Teachers\Rules;

use App\Modules\Teachers\Teacher;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TeacherCourseDetailsRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = request()->user();
        $teacher = Teacher::with('courses')->where('user_id',$user->id)->first();

        if (!$teacher) {
            $fail('Teacher not found.');
            return;
        }
        $exist = $teacher->courses->where(function ($course) use ($value) {
            return $course->pivot->id == $value;
        });
        
        if (!$exist) {
            $fail('Course is not valid.');
        }
    }
}


