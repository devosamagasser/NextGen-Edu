<?php

namespace App\Modules\Departments\Validation;

use App\Http\Requests\AbstractApiRequest;

class DepartmentUpdateRequest extends  AbstractApiRequest
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
        $id = request('department');
        return [
            'name' => "required|string|unique:departments,name,{$id}",
            'description' => 'nullable|string'
        ];
    }
}
