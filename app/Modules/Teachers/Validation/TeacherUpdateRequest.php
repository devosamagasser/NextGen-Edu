<?php

namespace App\Modules\Teachers\Validation;

use App\Http\Requests\AbstractApiRequest;

class TeacherUpdateRequest extends AbstractApiRequest
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
            'name' => 'nullable|string|min:3',
            'department_id' => 'nullable|integer|exists:departments,id',
            'description' => 'nullable|string'
        ];
    }
}
