<?php

namespace App\Http\Requests\Dashboard;

use App\Http\Requests\AbstractApiRequest;
use App\Rules\HallValidationRule;

class HallUpdateRequest extends AbstractApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('Super admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'building_id' => 'nullable|integer|exists:buildings,id',
            'code' => ['nullable','string',new HallValidationRule($this->building_id,request()->hall)],
            'floor' => 'nullable|integer'
        ];
    }
}
