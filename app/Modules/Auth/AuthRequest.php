<?php

namespace App\Modules\Auth;

use App\Http\Controllers\Abstract\AbstractAuthController;
use App\Http\Requests\AbstractApiRequest;

class AuthRequest extends AbstractApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => "required|email|exists:users,email",
            'password' => 'required|string|min:7',
        ];
    }

    public function messages()
    {
        return [
            'email.exists' => 'the credentials  doesn\'t match our records',
        ];
    }
}
