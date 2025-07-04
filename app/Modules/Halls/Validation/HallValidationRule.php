<?php

namespace App\Modules\Halls\Validation;

use App\Modules\Halls\Hall;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class HallValidationRule implements ValidationRule
{

    public function __construct(public $buildingId,public $hallId = null)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $check = Hall::where('code', $value)
            ->where('building_id', $this->buildingId);
        if($this->hallId)
            $check = $check->where('id','<>',$this->hallId);
        if ($check->exists())
            $fail('this hall is already exists in this building');
    }
}
