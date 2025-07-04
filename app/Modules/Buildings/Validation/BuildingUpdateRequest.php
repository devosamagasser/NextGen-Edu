<?php

namespace App\Modules\Buildings\Validation;

use App\Http\Requests\AbstractApiRequest;

class BuildingUpdateRequest extends AbstractApiRequest
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
        $id = request()->building;
        return [
            'code' => "required|integer|unique:buildings,code,{$id}",
            'name' => "nullable|string|unique:buildings,name,{$id}",
            'latitude' => "required",
            'longitude' => "required"
        ];
    }
}
