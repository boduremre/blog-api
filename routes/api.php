<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// php artisan make:controller Api/XController --api --model=X

// Herkese Açık Rotalar
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/logout-all', [AuthController::class, 'logout_all']);
Route::put('/update-password', [AuthController::class, 'update_password']);

// API Test Route
Route::get('/test', function () {
    return response()->json(
        [
            'api_version' => '1.0.0',
            'app_name' => 'Blog Rest API',
            'developer' => 'Emre Bodur',
            'documentation' => 'https://api.example.com/docs',
            'contact' => 'https://api.example.com/contact',
            'support' => 'https://api.example.com/support',
            'terms_of_service' => 'https://api.example.com/terms',
            'privacy_policy' => 'https://api.example.com/privacy',
            'license' => 'MIT License',
            'message' => 'Welcome to Blog Rest API',
            'status' => 'API is running',
            'server_timestamp' => now()->toDateTimeString()
        ]
    );
});

Route::apiResource('posts', PostController::class)->missing(function (Request $request) {
    return response()->json([
        'success' => false,
        'message' => 'Kayıt bulunamadı!'
    ], 404);
});;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
