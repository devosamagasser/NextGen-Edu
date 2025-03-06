<?php

namespace App\Modules\Halls\Validation;

use App\Http\Requests\AbstractApiRequest;

class HallStoreRequest extends AbstractApiRequest
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
            'building_id' => 'required|integer|exists:buildings,id',
            'code' => ['required',new HallValidationRule($this->building_id)],
            'floor' => 'required|integer'
        ];
    }
}
