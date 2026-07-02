<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\UsersBotController;
use App\Http\Controllers\AiUsageController;


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
Route::post('/chats', [ChatController::class,'store']);
Route::get('/recent-chat', [ChatController::class, 'recent']);

Route::post('/send', [MessageController::class,'send']);

Route::post('/handover', [BotController::class,'handover']);

Route::get('/stats',[StatsController::class,'index']);
Route::get('/chart',[StatsController::class,'chart']);
Route::get('/wordcloud', [StatsController::class, 'wordcloud']);
Route::get('/stats/ai', [StatsController::class, 'aiStats']);
Route::get('/ai-chart', [StatsController::class, 'aiChart']);


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

Route::get('/hot-leads',[StatsController::class,'hotLeads']);

Route::get('/users-bot', [UsersBotController::class, 'index']);

Route::get('/users-bot/summary', [UsersBotController::class, 'summary']);


/*
|--------------------------------------------------------------------------
| USER BOT
|--------------------------------------------------------------------------
*/

Route::get(
    '/users-bot',
    [UsersBotController::class, 'index']
);

Route::get(
    '/users-bot/summary',
    [UsersBotController::class, 'summary']
);

Route::get(
    '/users-bot/{nomor}',
    [UsersBotController::class, 'show']
);

Route::post('/ai/check-limit', [AiUsageController::class, 'checkLimit']);
Route::get('/ai/usages', [AiUsageController::class, 'index']);

