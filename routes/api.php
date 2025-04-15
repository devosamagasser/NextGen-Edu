<?php


use App\Modules\Auth\AuthController;
use App\Modules\Users\UserController;
use Illuminate\Support\Facades\Route;
use App\Modules\Quizzes\QuizzesController;

Route::post('/login',[AuthController::class,'login']);


Route::group(['middleware'=>['auth','role:Student']],function (){
    Route::delete('/logout',[AuthController::class,'logout']);
    Route::get('/node/user', [UserController::class, 'getUser']);
    Route::get('/quizzes',[QuizzesController::class,'index']);
    Route::get('quizzes/{id}/start', [QuizzesController::class, 'startStudentQuiz']);
    Route::post('quizzes/{id}/submit', [QuizzesController::class, 'submitAnswers']);
});