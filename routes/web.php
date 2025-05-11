<?php

use Illuminate\Support\Facades\Route;

use App\Modules\ChatBot\ChatBotController;

Route::post('/chat/send', [ChatBotController::class, 'send']);
Route::get('/chat', [ChatBotController::class, 'index']);
