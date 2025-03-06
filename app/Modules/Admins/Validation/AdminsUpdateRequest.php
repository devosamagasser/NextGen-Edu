<?php

namespace App\Modules\Admins\Validation;

use App\Http\Requests\AbstractApiRequest;

class AdminsUpdateRequest extends AbstractApiRequest
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
        $id = request()->admin;
        return [
            'name' => 'nullable|string|min:3',
            'email' => "nullable|email|unique:users,email,{$id}",
            'password' => 'nullable|string|min:7'
        ];
    }
}
