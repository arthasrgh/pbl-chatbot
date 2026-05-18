<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\UserController;

Route::post('/login',
    [AuthController::class,'login']
);

Route::get('/users',
    [UserController::class,'index']
);

Route::post('/users',
    [UserController::class,'store']
);

Route::put('/users/{id}',
    [UserController::class,'update']
);

Route::delete('/users/{id}',
    [UserController::class,'delete']
);

Route::get('/chats', [ChatController::class,'index']);
Route::get('/chats/{nomor}', [ChatController::class,'show']);

Route::post('/send', [MessageController::class,'send']);

Route::post('/handover', [BotController::class,'handover']);

Route::get('/stats',[StatsController::class,'index']);
Route::get('/chart',[StatsController::class,'chart']);

Route::get('/users',[UserController::class,'index']);
Route::post('/users/toggle',[UserController::class,'toggle']);
Route::delete('/users/{id}',[UserController::class,'delete']);