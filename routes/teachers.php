<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Quizzes\QuizzesController;
use App\Modules\Teachers\TeachersController;
use App\Modules\Assignments\AssignmentController;
use App\Modules\Announcments\AnnouncementController;



Route::group(['middleware'=>['auth','role:Teacher']],function (){
    Route::get('/courses',[TeachersController::class,'myCourses']);
    Route::get('/departments',[TeachersController::class,'myDepartments']);
    Route::get('/semesters',[TeachersController::class,'mySemesters']);
    Route::apiResource('quizzes',QuizzesController::class);
    Route::apiResource('assignments',AssignmentController::class);
    Route::apiResource('/announcements', AnnouncementController::class);
    Route::get('/my-announcements', [AnnouncementController::class,'showMine']);
});
