<?php

namespace App\Modules\Users;

use Exception;
use App\Models\User;
use App\Facades\ApiResponse;
use App\Facades\FileHandler;
use App\Modules\Students\Student;
use App\Modules\Auth\AuthServices;
use App\Modules\Users\UserResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
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
        try {
            if (str_starts_with($code, '3')) {
                $user = User::whereHas('teachers', function ($query) use ($code) {
                    $query->where('uni_code', $code);
                });
            } else {
                $user = User::whereHas('students', function ($query) use ($code) {
                    $query->where('uni_code', $code);
                });
            }
            $user = $user->firstOrFail();
            AuthServices::loadUserRelations($user);
            return ApiResponse::success(new UserResource($user));
        } catch (\Exception $e) {
            throw new Exception;
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

        if($user->type == 'Student'){
            Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer kfxuzk1pQESIimcee9rivOXGttoHiC8IlXaBFxhc3Y',
            ])->put('https://ngu-question-hub.azurewebsites.net/users/update', [
                'user' =>  [           
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'type' => $user->type,
                    'avatar' => $user->avatar_url,
                    'nationality' => $user->students->nationality,
                    'uni_code' => $user->students->uni_code,
                    'personal_id' => $user->students->personal_id,
                    'group' => $user->students->group,
                    'semester' =>[
                        'id' => $user->students->semester->id,
                        'name' => $user->students->semester->name
                    ],
                    'department' => [
                        'id' => $user->students->department->id,
                        'name' => $user->students->department->name
                    ],
                    'group' => $user->students->group,
                ]
            ]);

        }
        elseif($user->type == 'Teacher'){
            Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer kfxuzk1pQESIimcee9rivOXGttoHiC8IlXaBFxhc3Y',
            ])->put('https://ngu-question-hub.azurewebsites.net/users/update', [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'type' => $user->type,
                    'avatar' => $user->avatar_url,
                    'uni_code' =>  $user->teachers->uni_code,
                    'description' =>  $user->teachers->description,
                    'department' => [ 
                        'id' => $user->teachers->department_id,
                        'name' => $user->teachers->department->name
                    ]
                ]
            ]);
        }
        
        return ApiResponse::message('User updated successfully');
    }
}
