<?php

namespace App\Modules\Teachers\Validation;

use App\Http\Requests\AbstractApiRequest;

class TeacherStoreRequest extends AbstractApiRequest
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
            'name' => 'required|string|min:3',
            'department_id' => 'required|integer|exists:departments,id',
            'description' => 'nullable|string'
        ];
    }
}
