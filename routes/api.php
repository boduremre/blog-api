<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// php artisan make:controller Api/XController --api --model=X

// Herkese Açık Rotalar
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

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

// Korumalı Rotalar (Bearer Token Gerektirir)
Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('/tags', TagController::class)->missing(function (Request $request) {
        return response()->json([
            'success' => false,
            'message' => 'Kayıt bulunamadı!'
        ], 404);
    });

    // Kategoriler
    Route::apiResource('/categories', CategoryController::class)->missing(function (Request $request) {
        return response()->json([
            'success' => false,
            'message' => 'Kayıt bulunamadı!'
        ], 404);
    });

    // Tüm user işlemleri için tek satır yeterli (index, store, show, update, destroy)
    Route::apiResource('/users', UserController::class)->missing(function (Request $request) {
        return response()->json([
            'success' => false,
            'message' => 'Kayıt bulunamadı!'
        ], 404);
    });

    Route::apiResource('posts', PostController::class)->missing(function (Request $request) {
        return response()->json([
            'success' => false,
            'message' => 'Kayıt bulunamadı!'
        ], 404);
    });

    // Şifre güncelleme rotası    
    Route::post('/update-password', [AuthController::class, 'update_password']);

    // Tek cihazdan çıkış yapma rotası
    Route::post('/logout', [AuthController::class, 'logout']);

    // Tüm cihazlardan çıkış yapma rotası
    Route::post('/logout-all-devices', [AuthController::class, 'logout_all']);

    // CSRF Token Göster
    Route::get('/csrf-token', function (Request $request) {
        return ['csrf_token' => $request->cookie('XSRF-TOKEN')];
    });

    // Kullanıcı bilgilerini döndüren rota
    Route::get('/user', function (Request $request) {
        return [
            'bearer_token' => $request->bearerToken(),
            'csrf_token' => $request->cookie('XSRF-TOKEN'),
            'user' => $request->user()
        ];
    });
});
