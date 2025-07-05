<?php


use App\Modules\Auth\AuthController;
use App\Modules\Users\UserController;
use Illuminate\Support\Facades\Route;
use App\Modules\Halls\HallsController;
use App\Modules\Table\TableController;
use App\Modules\Courses\CoursesController;
use App\Modules\Quizzes\QuizzesController;
use App\Modules\Students\StudentsController;
use App\Modules\Buildings\BuildingsController;
use App\Modules\Assignments\AssignmentController;
use App\Modules\Announcments\AnnouncementController;
use App\Modules\CourseMaterials\CourseMaterialsController;

Route::post('/login',[AuthController::class,'login']);

Route::get('/hall/enter/{hall_id}', [HallsController::class, 'enter']);
Route::get('/hall/exit/{hall_id}', [HallsController::class, 'exit']);


Route::get('/attendance/{hall_id}/{student_tag}', [StudentsController::class, 'attendance']);

Route::group(['middleware'=>'auth'],function (){
    Route::get('/node/user', [UserController::class, 'profile']);
    Route::get('/node/user/{code}', [UserController::class, 'profileByCode']);
});

Route::group(['middleware'=>['auth','role:Student']],function (){
    Route::delete('/logout',[AuthController::class,'logout']);
    Route::get('/profile',[UserController::class,'profile']);
    Route::post('/update',[UserController::class,'update']);
    
    Route::get('/courses',[StudentsController::class,'myCourses']);
    Route::get('/courses/{id}',[CoursesController::class,'show']);
    Route::prefix('quizzes')->group(function () {
        Route::get('/', [QuizzesController::class, 'index']);
        Route::get('{id}/start', [QuizzesController::class, 'startStudentQuiz']);
        Route::post('{id}/submit', [QuizzesController::class, 'submitAnswers']);
        Route::get('/answers/{id}', [QuizzesController::class, 'quizWithStudentAnswers']);
    });
    Route::get('/announcements', [AnnouncementController::class, 'index']);
    Route::get('/table', [TableController::class,'index']);

    Route::prefix('assignments')->group(function () {
        Route::get('', [AssignmentController::class, 'index']);
        Route::get('{id}', [AssignmentController::class, 'show']);
        Route::post('{id}/submit', [AssignmentController::class, 'submit']);
    });
    Route::get('/course-materials/{id}', [CourseMaterialsController::class,'index']);
    Route::get('/course-materials/{id}/show', [CourseMaterialsController::class,'show']);
});
Route::group(['middleware'=>['auth','role:Student|Teacher']],function (){
        Route::get('/buildings',[BuildingsController::class, 'index']);
        Route::get('/halls',[HallsController::class, 'all']);
});

