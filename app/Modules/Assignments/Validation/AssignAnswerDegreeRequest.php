<?php

namespace App\Modules\Assignments\Validation;

use App\Http\Requests\AbstractApiRequest;

class AssignAnswerDegreeRequest extends AbstractApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('Teacher');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'degree' => 'required|integer',
        ];
    }
}
