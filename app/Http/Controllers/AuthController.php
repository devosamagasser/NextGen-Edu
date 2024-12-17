<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Services\AuthServices;
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
