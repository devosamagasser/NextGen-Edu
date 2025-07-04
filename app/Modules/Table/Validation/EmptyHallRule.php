<?php

namespace App\Modules\Table\Validation;

use App\Modules\Table\Models\Session;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EmptyHallRule implements ValidationRule
{
    protected ?int $department;
    protected ?int $semester;

    public function __construct($department = null, $semester = null)
    {
        $this->semester = $semester;
        $this->department = $department;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $day = request()->input('day');
        $from = request()->input('from');
        $to = request()->input('to');

        $exists = Session::where('hall_id', $value)
        ->when($this->department, function ($query) {
            $query->where('department_id', '<>',$this->department);
        })
        ->when($this->semester, function ($query) {
            $query->where('semester_id', '<>',$this->semester);
        })
        ->where('status', 'in time')
        ->where('day', $day)
        ->where(function ($query) use($from, $to) {
            $query->whereBetween('from', [$from, $to])
            ->orWhereBetween('to', [$from, $to])
            ->orWhere(function ($query) use($from, $to) {
                $query->where('from', '<=', $from)
                ->where('to', '>=', $to);
            });
        })
        ->exists();
        
        if ($exists) {
            $fail('The hall is not empty at this time.');
        }
    }
}
