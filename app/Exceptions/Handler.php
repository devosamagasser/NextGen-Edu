<?php

namespace App\Exceptions;

use Throwable;
use App\Facades\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (AuthenticationException $e, $request) {
            return ApiResponse::unauthorized('Unauthenticated.');
        });

        $this->renderable(function (AuthorizationException $e, $request) {
            return ApiResponse::unauthorized('Unauthorized.');
        });

        $this->renderable(function (AccessDeniedHttpException $e, $request) {
            return ApiResponse::message($e->getMessage(), $e->getStatusCode());
        });

        $this->renderable(function (ModelNotFoundException $e, $request) {
            return ApiResponse::notFound($e->getMessage());
        });

        $this->renderable(function (NotFoundHttpException $e, $request) {
            return ApiResponse::notFound($e->getMessage());
        });

        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            return ApiResponse::message('HTTP method not allowed.', 405);
        });

        $this->renderable(function (HttpException $e, $request) {
            return ApiResponse::message($e->getMessage(), $e->getStatusCode());
        });

        $this->renderable(function (HttpResponseException $e, $request) {
            return ApiResponse::message($e->getMessage(), $e->getResponse()->getStatusCode());
        });

        $this->renderable(function (Throwable $e, $request) {
            return ApiResponse::serverError($e->getMessage());
        });

    }
}
