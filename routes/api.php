<?php


use App\Modules\Auth\AuthController;
use App\Modules\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login',[AuthController::class,'login']);

Route::delete('/logout',[AuthController::class,'logout']);

Route::get('/node/user', [UserController::class, 'getUser']);


