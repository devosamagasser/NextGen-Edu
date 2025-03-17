<?php

namespace App\Exceptions;

use Throwable;
use App\Facades\ApiResponse;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
                return ApiResponse::unauthorized('unauthenticated process');
        });

        $this->renderable(function (ModelNotFoundException $e, $request) {
            return ApiResponse::notFound();
        });

        $this->renderable(function (NotFoundHttpException $e, $request) {
            return ApiResponse::notFound();
        });

        $this->renderable(function (HttpResponseException $e, $request) {
            return ApiResponse::notFound();
        });

        $this->renderable(function (\Exception $e, $request) {
            return ApiResponse::serverError();
        });

    }
}
