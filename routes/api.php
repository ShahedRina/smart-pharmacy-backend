<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminAuthController;

// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Admin Routes
Route::prefix('admin')->group(function () {

    // Admin Login - بدون Token
    Route::post('/login', [AdminAuthController::class, 'adminLogin']);

    // Protected Admin Routes
    Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {

        Route::post('/admins', [AdminAuthController::class, 'createAdmin']);

        Route::post('/delivery', [AdminAuthController::class, 'createDelivery']);

        Route::post('/logout', [AdminAuthController::class, 'adminLogout']);

    });
});

// Protected User and Delivery Routes
Route::middleware('auth:sanctum')->group(function () {

    // Delivery Routes
    Route::middleware('role:delivery')->group(function () {

        Route::get('/delivery/dashboard', function () {
            return response()->json([
                'message' => 'Welcome Delivery'
            ]);
        });

    });

    // User Routes
    Route::middleware('role:user')->group(function () {

        Route::get('/user/dashboard', function () {
            return response()->json([
                'message' => 'Welcome User'
            ]);
        });

    });

});
