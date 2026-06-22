<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FaqController;

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

Route::post(
    '/forgot-password',
    [AuthController::class,'forgotPassword']
);

Route::post(
    '/reset-password',
    [AuthController::class,'resetPassword']
);

Route::get('/chats', [ChatController::class,'index']);
Route::get('/chats/{nomor}', [ChatController::class,'show'])
    ->where('nomor', '.*');

Route::post('/send', [MessageController::class,'send']);

Route::post('/handover', [BotController::class,'handover']);

Route::get('/stats',[StatsController::class,'index']);
Route::get('/chart',[StatsController::class,'chart']);

Route::get('/users',[UserController::class,'index']);
Route::post('/users/toggle',[UserController::class,'toggle']);
Route::delete('/users/{id}',[UserController::class,'delete']);

Route::prefix('admins')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'delete']);
});

Route::get('/faqs', [FaqController::class, 'index']);
Route::get('/faqs/search/{keyword}', [FaqController::class, 'search']);