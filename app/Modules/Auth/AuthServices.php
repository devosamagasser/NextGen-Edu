<?php

namespace App\Modules\Auth;

use App\Models\User;
use App\Facades\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PharIo\Version\Exception;
use App\Modules\Users\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthServices
{

    public function login($request)
    {
        try {
            $credentials = $request->only(['email','password']);

            if (! $user = $this->checkUser($credentials)) {
                return ApiResponse::message('Your credentials doesn\'t match our records',Response::HTTP_UNAUTHORIZED);
            }
            
            // if (! $this->checkDevice($user,$request)) {
            //     return ApiResponse::message('not allowed to login from mobile app',Response::HTTP_FORBIDDEN);
            // }
            
            $token = $this->generateToken($user,$user->type);
            return $this->respondWithToken($user,$token);

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('User not found');

        }  catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|Response
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return ApiResponse::message('Logout successfully');
        } catch (Exception $e) {
            throw new \Exception($e->getMessage());  
        }

    }

    /**
     * @param $credentials
     * @return mixed
     * @throws \Exception
     */
    protected function checkUser($credentials): mixed
    {
        try {
            $user = User::whereEmail($credentials['email'])->firstOrFail();
            if (! Hash::check($credentials['password'],$user->password))
                return false;
            return $user;
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException();
        } catch (Exception $e) {
            throw new \Exception();
        }
    }

    /**
     * @param $user
     * @param $device
     * @return bool
     */
    // protected function checkDevice($user,$request): bool
    // {
    //     if(!$request->hasHeader('X-Device-Type'))
    //         return false;
    //     if ($request->header('X-Device-Type') == 'mobile')
    //         return $user->type == 'Student' || $user->type == 'Teacher';
    //     return true;
    // }

    /**
     * @param $user
     * @param $tokenName
     * @return mixed
     */
    protected function generateToken($user,$tokenName): mixed
    {
        return $user->createToken($tokenName)->plainTextToken;
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     */
    protected function respondWithToken($user, $token)
    {
        $this->loadUserRelations($user);
    
        return ApiResponse::success([
            [
                'access_token' => $token,
                'token_type' => 'bearer',
                'user' => new UserResource($user)
            ]
        ], 'logged in successfully');
    }
    
    public static function loadUserRelations($user)
    {
        switch ($user->type) {
            case 'Student':
                $user->load('students');
                break;
            case 'Teacher':
                $user->load('teachers');
                break;
            case 'Super admin':
            case 'Admin':
                break;
            default:
                throw new AuthorizationException("User type not found");            
        }
    }
    


}
