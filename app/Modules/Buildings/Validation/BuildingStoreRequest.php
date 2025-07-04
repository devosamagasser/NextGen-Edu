<?php

namespace App\Modules\Buildings\Validation;

use App\Http\Requests\AbstractApiRequest;

class BuildingStoreRequest extends AbstractApiRequest
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
            'code' => 'required|integer|unique:buildings,code',
            'name' => 'nullable|string|unique:buildings,name',
            'latitude' => 'required',
            'longitude' => 'required'
        ];
    }
}
