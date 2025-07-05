<?php

namespace App\Modules\Users;

use App\Models\User;
use App\Facades\ApiResponse;
use App\Facades\FileHandler;
use App\Modules\Auth\AuthServices;
use App\Modules\Users\UserResource;
use App\Http\Controllers\Controller;
use App\Modules\Students\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\AuthenticationException;

class UserController extends Controller
{

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

    public function profileByCode($code)
    {
        try{
            if(str_starts_with($code, '3')($code,'3')){
                $user = User::where(function($query) use ($code) {
                    $query->teachers()->where('code', $code);
                })->firstOrFail();

            } else{
                $user = User::where(function($query) use ($code) {
                    $query->students()->where('code', $code);
                })->firstOrFail();
            }
            $user = $user->firstOrFail();
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
