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

    public function profile()
    {
        return [
            "id" => 6,
            "name" => "osama mohamed",
            "email" => "20812025000001@zu.edu.eg",
            "type" => "Student",
            "avatar" => "nextgenedu-database.azurewebsites.net/storage/avatar.png",
            "nationality" => "National",
            "uni_code" => "20812025000001",
            "personal_id" => "3020812131212",
            "level" => 7,
            "class" => "فرقة ثالثة",
            "department" => "الهندسة المعمارية",
            "group" => 1
        ];
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
