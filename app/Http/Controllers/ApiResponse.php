<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentResource;
use Illuminate\Http\Response;

class ApiResponse extends Controller
{
    /**
     * @param $info
     * @param $message
     * @param $code
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|Response
     */
    function apiFormat($info,$message = null,$code= Response::HTTP_OK)
    {
        $response = [
            'code' => $code,
        ];
        if ($message)
            $response['message'] = $message;
        if($info){
            $key = key($info);
            $response[$key] = $info[$key];
        }

        return Response($response,Response::HTTP_OK);
    }

    public function notFound($message)
    {
        return $this->apiFormat(
            null,
            $message,
            Response::HTTP_NOT_FOUND);
    }

    public function serverError($message = 'Faild to process this action, please try again.')
    {
        return $this->apiFormat(
            null,
            $message,
            Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function success($data,$message = null,$code = Response::HTTP_OK)
    {
        return $this->apiFormat(['data' => $data], $message,$code);
    }

    public function created($data,$message = 'created successfully')
    {
        return $this->success($data, $message,Response::HTTP_CREATED);
    }

    public function updated($data,$message = 'updated successfully')
    {
        return $this->success($data, $message);
    }

    public function message($message,$code = Response::HTTP_OK)
    {
        return $this->apiFormat(null, $message,$code);
    }

    public function faild($errors,$message,$code)
    {
        return $this->apiFormat(['errors' => $errors], $message,$code);
    }

    public function validationError($errors,$message = 'validation error')
    {
        return $this->faild($errors, $message,Response::HTTP_BAD_REQUEST);
    }






}
