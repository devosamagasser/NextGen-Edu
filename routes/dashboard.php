<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dashboard\{
    AdminsController,
    DepartmentsController,
    BuildingsController,
    HallsController,
    CoursesController,
    TeachersController,
    StudentsController,
};
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

//Route::group(['middleware'=>['auth','role:Admin']],function (){
    Route::get('/students/export',[StudentsController::class,'export']);
    Route::post('/students/import',[StudentsController::class,'import']);
    Route::apiResource('/students',StudentsController::class);
//});


