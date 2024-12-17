<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use PharIo\Version\Exception;

class AuthServices
{


    public function login($request)
    {
        try {
            $credentials = $request->only(['email','password']);

            if (! $user = $this->checkUser($credentials)) {
                return apiResponse(null,'Your credentials doesn\'t match our records' , Response::HTTP_UNAUTHORIZED);
            }

            if (! $this->checkDevice($user,$request)) {
                return apiResponse(null,'not allowed to login from mobile app' , Response::HTTP_FORBIDDEN);
            }

            $token = $this->generateToken($user,$user->type);

            return $this->respondWithToken($user,$token);

        } catch (ModelNotFoundException $e) {
            return apiResponse(null, 'User not found', Response::HTTP_NOT_FOUND);

        }  catch (\Exception $e) {
            return apiResponse(null,'please try again later ' , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|Response
     */
    public function logout($request): \Illuminate\Foundation\Application|Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return apiResponse(null,'Logout successfully' , Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $e) {
            return apiResponse(null,'please try again later ' , Response::HTTP_INTERNAL_SERVER_ERROR);
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
    protected function checkDevice($user,$request): bool
    {
        if(!$request->hasHeader('X-Device-Type'))
            return false;
        if ($request->header('X-Device-Type') == 'mobile')
            return $user->type == 'Student' || $user->type == 'Teacher';
        return true;
    }

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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|Response
     */
    protected function respondWithToken($user,$token): \Illuminate\Foundation\Application|Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        return apiResponse([
            'data' =>
                [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'user' => $user
                ]
        ],'logged in successfully');
    }


}
