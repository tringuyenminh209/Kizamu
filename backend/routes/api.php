<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working!',
        'time' => now()
    ]);
});

// 認証ルート(レート制限付き)
Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:3,1'); // 1分あたり3リクエスト

Route::post('/login', [AuthController::class, 'login'])
    ->name('login')
    ->middleware('throttle:5,1'); // 1分あたり5リクエスト

Route::middleware('auth:sanctum')-> group(function(){

    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    Route::post('/user/fcm-token', [AuthController::class, 'updateFCMToken']);

    // タスクルート（リソースルートは最後に配置する必要がある）
    Route::apiResource('tasks', TaskController::class);
});
