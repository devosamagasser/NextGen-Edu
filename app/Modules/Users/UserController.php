<?php

namespace App\Modules\Users;

use App\Models\User;
use App\Facades\ApiResponse;
use App\Facades\FileHandler;
use App\Modules\Auth\AuthServices;
use App\Modules\Users\UserResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
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

    public function profile()
    {
        try{
            $user = request()->user();
            AuthServices::loadUserRelations($user);
            return ApiResponse::success(new UserResource($user));
        }catch (\Exception $e){
            throw new AuthenticationException;
        }
    }

    public function update(UserUpdateRequest $request)
    {
        $user = request()->user();
        $data = [];
        if ($request->hasFile('avatar')) {
            $data['avatar'] = FileHandler::updateFile(
                $request->file('avatar'), 
                $user->avatar, 
                'users',
                $request->file('avatar')->getClientOriginalExtension()
            );
        }
        if($request->filled('password')){
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);

        return ApiResponse::message('User updated successfully');
    }
}
