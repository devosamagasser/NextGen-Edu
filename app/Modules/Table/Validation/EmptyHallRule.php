<?php

namespace App\Modules\Table\Validation;

use App\Modules\Table\Models\Session;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EmptyHallRule implements ValidationRule
{
    protected ?int $id;

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $date = request()->input('date');
        $from = request()->input('from');
        $to = request()->input('to');

        if ($date) {
            $exists = Session::where('hall_id', $value)
            ->where('status', 'in time')
            ->where('date', $date)
            ->where(function ($query) use($date, $from, $to) {
                $query->whereBetween('from', [$from, $to])
                ->orWhereBetween('to', [$from, $to])
                ->orWhere(function ($query) use($date, $from, $to) {
                    $query->where('from', '<=', $from)
                    ->where('to', '>=', $to);
                });
            })
            ->when($this->id, fn($query) => $query->where('id', '!=', $this->id))
            ->exists();
            
            if ($exists) {
                $fail('The hall is not empty at this time.');
            }
        }
    }
}
