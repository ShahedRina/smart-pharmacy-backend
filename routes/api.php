<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    // الصيدلي
    Route::middleware('role:pharmacist')->group(function () {
        Route::get('/pharmacist/dashboard', function () {
            return response()->json([
                'message' => 'Welcome Pharmacist'
            ]);
        });
    });

    // مندوب التوصيل
    Route::middleware('role:delivery')->group(function () {
        Route::get('/delivery/dashboard', function () {
            return response()->json([
                'message' => 'Welcome Delivery'
            ]);
        });
    });
    // المستخدم
    Route::middleware('role:user')->group(function () {
        Route::get('/user/dashboard', function () {
            return response()->json([
                'message' => 'Welcome User'
            ]);
        });
    });

});