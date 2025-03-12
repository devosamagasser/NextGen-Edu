<?php


use App\Modules\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login',[AuthController::class,'login']);

Route::delete('/logout',[AuthController::class,'logout']);

Route::get('/test',function (){
    return 'abaza';
});