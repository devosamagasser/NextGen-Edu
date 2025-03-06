<?php


use App\Modules\Admins\AdminsController;
use App\Modules\Auth\AuthController;
use App\Modules\Buildings\BuildingsController;
use App\Modules\Courses\CoursesController;
use App\Modules\Departments\DepartmentsController;
use App\Modules\Halls\HallsController;
use App\Modules\Students\StudentsController;
use App\Modules\Table\TableController;
use App\Modules\Teachers\{TeachersController,};
use Illuminate\Support\Facades\Route;

Route::post('/login',[AuthController::class,'login']);
Route::group(['middleware'=>['auth','role:Super admin']],function (){
    Route::apiResource('/admin',AdminsController::class);
    Route::apiResource('/teachers',TeachersController::class);
    Route::apiResource('/department',DepartmentsController::class);
    Route::apiResource('/building',BuildingsController::class);
    Route::prefix('building')->group(function(){
        Route::get('/{id}/halls', [HallsController::class,'index']);
        Route::apiResource('/halls',HallsController::class)->except('index');
    });
    Route::apiResource('/courses',CoursesController::class);
});

Route::group(['middleware'=>['auth','role:Admin']],function (){
    Route::get('/students/export',[StudentsController::class,'export']);
    Route::post('/students/import',[StudentsController::class,'import']);
    Route::apiResource('/students',StudentsController::class);
    Route::apiResource('/table',TableController::class);
    Route::post('/table/{table}',[TableController::class,'update']);
});


