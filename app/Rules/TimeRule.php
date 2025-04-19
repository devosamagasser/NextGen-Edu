<?php

namespace App\Rules;

use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;

class TimeRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $date = request('date') ?? '';
        $dateTime = $date . ' ' . $value;
        Carbon::parse($dateTime);
        if(now()->gt($dateTime)) {
            $fail('The ' . $attribute . ' must be a valid future date and time.');
        }
    }
}
