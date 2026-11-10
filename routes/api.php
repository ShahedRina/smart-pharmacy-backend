<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\MedicineController;
use App\Http\Controllers\Api\ProfileController;

// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// Public Categories Routes

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

// Public Medicines Routes
Route::get('/medicines', [MedicineController::class, 'index']);
Route::get('/medicines/{medicine}', [MedicineController::class, 'show']);

// Admin Login - بدون Token
Route::post('/admin/login', [AdminAuthController::class, 'adminLogin']);


// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [ProfileController::class, 'show']);
    Route::put('/me', [ProfileController::class, 'update']);
    Route::put('/me/password', [ProfileController::class, 'updatePassword']);

    // Admin Routes
    Route::middleware('is_admin')->prefix('admin')->group(function () {

        Route::post('/admins', [AdminAuthController::class, 'createAdmin']);
        Route::post('/delivery', [AdminAuthController::class, 'createDelivery']);
        Route::post('/logout', [AdminAuthController::class, 'adminLogout']);

        // Categories Management
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

        // Medicines Management
        Route::post('/medicines', [MedicineController::class, 'store']);
        Route::put('/medicines/{medicine}', [MedicineController::class, 'update']);
        Route::delete('/medicines/{medicine}', [MedicineController::class, 'destroy']);
    });

   
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