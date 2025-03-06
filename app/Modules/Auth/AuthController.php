<?php

namespace App\Modules\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(public AuthServices $authServices)
    {
    }

    public function login(AuthRequest $request)
    {
        return $this->authServices->login($request);
    }
    
    public function logout(Request $request)
    {
        return $this->authServices->logout($request);
    }
}
