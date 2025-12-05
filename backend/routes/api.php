<?php

use App\Http\Controllers\AuthController;
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
