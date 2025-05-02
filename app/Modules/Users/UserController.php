<?php

namespace App\Modules\Users;

use App\Models\User;
use App\Facades\ApiResponse;
use App\Modules\Users\UserResource;
use App\Http\Controllers\Controller;
use Illuminate\Auth\AuthenticationException;

class UserController extends Controller
{
    public function getUser()
    {
        try{

            $user = request()->user();
            $user = User::when($user->type == 'Student', function($query){
                $query->with('students');
            })
            ->when($user->type == 'Teacher', function($query){
                $query->with('Teacher');
            })->findOrFail($user->id);
            return ApiResponse::success(new UserResource($user));
        }catch (\Exception $e){
            throw new AuthenticationException;
        }
    }
}
