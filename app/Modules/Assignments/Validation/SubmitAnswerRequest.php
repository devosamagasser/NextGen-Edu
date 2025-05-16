<?php

namespace App\Modules\Assignments\Validation;

use App\Http\Requests\AbstractApiRequest;

class SubmitAnswerRequest extends AbstractApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('Student');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:pdf,doc,docx,csv,jpg,png,jfif,xlsx',
        ];
    }
}
