<?php


use App\Modules\Auth\AuthController;
use App\Modules\Users\UserController;
use Illuminate\Support\Facades\Route;
use App\Modules\Quizzes\QuizzesController;
use App\Modules\Assignments\AssignmentController;

Route::post('/login',[AuthController::class,'login']);


Route::group(['middleware'=>['auth','role:Student']],function (){
    Route::delete('/logout',[AuthController::class,'logout']);
    Route::get('/node/user', [UserController::class, 'getUser']);
    Route::prefix('quizzes')->group(function () {
        Route::get('/', [QuizzesController::class, 'index']);
        Route::get('{id}/start', [QuizzesController::class, 'startStudentQuiz']);
        Route::post('{id}/submit', [QuizzesController::class, 'submitAnswers']);
    });

    Route::prefix('assignments')->group(function () {
        Route::get('', [AssignmentController::class, 'index']);
        Route::get('{id}', [AssignmentController::class, 'show']);
        Route::post('{id}/submit', [AssignmentController::class, 'submit']);
    });
});