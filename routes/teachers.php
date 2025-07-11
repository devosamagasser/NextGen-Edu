<?php

use App\Modules\Auth\AuthController;
use App\Modules\Users\UserController;
use Illuminate\Support\Facades\Route;
use App\Modules\Table\TableController;
use App\Modules\Quizzes\QuizzesController;
use App\Modules\Students\StudentsController;
use App\Modules\Teachers\TeachersController;
use App\Modules\Assignments\AssignmentController;
use App\Modules\Announcments\AnnouncementController;
use App\Modules\CourseMaterials\CourseMaterialsController;

Route::group(['middleware'=>['auth','role:Teacher']],function (){
    Route::delete('/logout',[AuthController::class,'logout']);
    Route::get('/profile',[UserController::class,'profile']);
    Route::post('/update',[UserController::class,'update']);
    
    Route::get('/courses',[TeachersController::class,'myCourses']);
    Route::get('/students/{id}',[StudentsController::class,'studentsCourse']);
    Route::get('/departments',[TeachersController::class,'myDepartments']);
    Route::get('/semesters',[TeachersController::class,'mySemesters']);
    Route::apiResource('quizzes',QuizzesController::class);
    Route::get('quizzes/answers/{id}/{student}', [QuizzesController::class, 'quizWithStudentAnswers']);
    Route::get('quizzes/answers/{id}', [QuizzesController::class, 'quizStudentsAnswers']);

    Route::apiResource('assignments',AssignmentController::class)->except(['update']);
    Route::post('assignments/{id}',[AssignmentController::class , 'update']);
    Route::put('assignments/answer/{assignmentId}',[AssignmentController::class, 'assignDegree']);
    
    Route::apiResource('/announcements', AnnouncementController::class);
    Route::get('/my-announcements', [AnnouncementController::class,'showMine']);
    Route::get('/table', [TableController::class,'index']);
    Route::put('/table/session/{session_id}', [TableController::class, 'postponeSessionByTeacher']);

    Route::group(['prefix'=>'course-materials'],function (){
        Route::get('/{id}', [CourseMaterialsController::class,'index']);
        Route::post('/{id}', [CourseMaterialsController::class,'store']);
        Route::get('/{id}/show', [CourseMaterialsController::class,'show']);
        Route::post('/{id}/update', [CourseMaterialsController::class,'update']);
        Route::delete('/{id}', [CourseMaterialsController::class,'destroy']);
    });

});
