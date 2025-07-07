<?php


use App\Modules\Auth\AuthController;
use App\Modules\Users\UserController;
use Illuminate\Support\Facades\Route;
use App\Modules\Halls\HallsController;
use App\Modules\Table\TableController;
use App\Modules\Admins\AdminsController;
use App\Modules\Courses\CoursesController;
use App\Modules\Students\StudentsController;
use App\Modules\Buildings\BuildingsController;
use App\Modules\Semesters\SemestersController;
use App\Modules\Teachers\{TeachersController,};
use App\Modules\Departments\DepartmentsController;
use App\Modules\Announcments\AnnouncementController;
use OpenApi\Annotations\Get;

Route::post('/login',[AuthController::class,'login']);
Route::controller(StudentsController::class)->group(function () {
    Route::get('/students/export', 'export');
});
Route::group(['middleware' => 'auth' ], function () {
    Route::group(['middleware'=>'role:Super admin'],function (){
        Route::get('/profile',[UserController::class,'profile']);
        Route::post('/update',[UserController::class,'update']);

        Route::get('/semesters',[SemestersController::class,'index']);
        Route::apiResource('/admin',AdminsController::class);
        Route::apiResource('/teachers',TeachersController::class);
        Route::post('/teachers/import', [TeachersController::class, 'import']);
        
        Route::apiResource('/department',DepartmentsController::class);
        Route::post('/department/import', [DepartmentsController::class,'import']);


        Route::apiResource('/building',BuildingsController::class);
        Route::prefix('building')->group(function(){
            Route::get('/{id}/halls', [HallsController::class,'index']);
            Route::apiResource('/halls',HallsController::class)->except('index');
        });
        Route::apiResource('/courses',CoursesController::class);
        Route::post('/courses/{id}/details', [CoursesController::class,'storeDetails']);
    });

    Route::group(['middleware' => 'role:Admin|Super admin' ], function () {

        Route::controller(StudentsController::class)->group(function () {
            Route::get('/students/export', 'export');
            Route::post('/students/import', 'import');
            Route::apiResource('/students', StudentsController::class);
        });
        
        Route::post('table/{department}/{semester}/manually', [TableController::class, 'manually']);
        Route::put('table/{department}/{semester}/update', [TableController::class, 'update']);
        Route::delete('table/{department}/{semester}/delete', [TableController::class, 'destroy']);
            
        Route::controller(TableController::class)->group(function () {
            Route::apiResource('/table', TableController::class);
            Route::post('/table/{table}', 'update');
        });
    });

    Route::group(['middleware' => 'role:Admin|Teacher' ], function () {
        Route::apiResource('/announcements', AnnouncementController::class);
        Route::get('/my-announcements', [AnnouncementController::class,'showMine']);
    });
    
});







