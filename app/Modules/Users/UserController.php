<?php

namespace App\Modules\Users;

use App\Models\User;
use App\Facades\ApiResponse;
use App\Modules\Users\UserResource;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function getUser()
    {
        $user = request()->user();
        $user = User::when($user->type == 'Student', function($query){
            $query->with('students');
        })->findOrFail($user->id);
        return ApiResponse::success(new UserResource($user));
    }
}
