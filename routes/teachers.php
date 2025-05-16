<?php

use App\Modules\Users\UserController;
use Illuminate\Support\Facades\Route;
use App\Modules\Table\TableController;
use App\Modules\Quizzes\QuizzesController;
use App\Modules\Teachers\TeachersController;
use App\Modules\Assignments\AssignmentController;
use App\Modules\Announcments\AnnouncementController;
use App\Modules\CourseMaterials\CourseMaterialsController;
use App\Modules\Students\StudentsController;

Route::group(['middleware'=>['auth','role:Teacher']],function (){
    Route::get('/profile',[UserController::class,'profile']);
    Route::post('/update',[UserController::class,'update']);
    
    Route::get('/courses',[TeachersController::class,'myCourses']);
    Route::get('/students/{id}',[StudentsController::class,'studentsCourse']);
    Route::get('/departments',[TeachersController::class,'myDepartments']);
    Route::get('/semesters',[TeachersController::class,'mySemesters']);
    Route::apiResource('quizzes',QuizzesController::class);

    Route::apiResource('assignments',AssignmentController::class);
    Route::put('assignments/answer/{id}',[AssignmentController::class, 'assignDegree']);

    
    Route::apiResource('/announcements', AnnouncementController::class);
    Route::get('/my-announcements', [AnnouncementController::class,'showMine']);
    Route::get('/table', [TableController::class,'index']);

    Route::group(['prefix'=>'course-materials'],function (){
        Route::get('/{id}', [CourseMaterialsController::class,'index']);
        Route::post('/{id}', [CourseMaterialsController::class,'store']);
        Route::get('/{id}/show', [CourseMaterialsController::class,'show']);
        Route::put('/{id}', [CourseMaterialsController::class,'update']);
        Route::delete('/{id}', [CourseMaterialsController::class,'destroy']);
    });
});
