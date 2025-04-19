<?php

namespace App\Modules\Teachers\Rules;

use App\Modules\Teachers\Teacher;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class InTeacherRelationRule implements ValidationRule
{
    protected string $relation;

    public function __construct(string $relation)
    {
        $this->relation = $relation;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = request()->user();
        if($user->type == 'Teacher'){
            $teacher = Teacher::with($this->relation)->where('user_id',$user->id)->first();
            $ids = $teacher->{$this->relation}->pluck('id')->toArray();
            if (!in_array($value, $ids)) {
                $fail(ucfirst($this->relation) . ' is not valid.');
            }
        }
    }
}

