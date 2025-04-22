<?php

use App\Modules\Assignments\AssignmentController;
use App\Modules\Quizzes\QuizzesController;
use Illuminate\Support\Facades\Route;
use App\Modules\Teachers\TeachersController;



Route::group(['middleware'=>['auth','role:Teacher']],function (){
    Route::get('/courses',[TeachersController::class,'myCourses']);
    Route::get('/departments',[TeachersController::class,'myDepartments']);
    Route::get('/semesters',[TeachersController::class,'mySemesters']);
    Route::apiResource('quizzes',QuizzesController::class);
    Route::apiResource('assignments',AssignmentController::class);
});
