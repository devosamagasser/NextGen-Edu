<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Quizzes\QuizzesController;
use App\Modules\Teachers\TeachersController;
use App\Modules\Assignments\AssignmentController;
use App\Modules\Announcments\AnnouncementController;
use App\Modules\CourseMaterials\CourseMaterialsController;

Route::group(['middleware'=>['auth','role:Teacher']],function (){
    Route::get('/courses',[TeachersController::class,'myCourses']);
    Route::get('/departments',[TeachersController::class,'myDepartments']);
    Route::get('/semesters',[TeachersController::class,'mySemesters']);
    Route::apiResource('quizzes',QuizzesController::class);
    Route::apiResource('assignments',AssignmentController::class);
    Route::apiResource('/announcements', AnnouncementController::class);
    Route::get('/my-announcements', [AnnouncementController::class,'showMine']);
    Route::get('/course-materials/{id}', [CourseMaterialsController::class,'index']);
    Route::post('/course-materials/{id}', [CourseMaterialsController::class,'store']);
    Route::get('/course-materials/{id}/show', [CourseMaterialsController::class,'show']);
    Route::put('/course-materials/{id}', [CourseMaterialsController::class,'update']);
    Route::delete('/course-materials/{id}', [CourseMaterialsController::class,'destroy']);
});
