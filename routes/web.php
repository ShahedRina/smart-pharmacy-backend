<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


Route::get('/login ', [AuthController::class ,'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class ,'login']) ;

Route::get('/register ', action: [AuthController::class ,'showsingupform'])->name('register');
Route::post('/register ', [AuthController::class ,'register']);

Route::post('/logout ', action: [AuthController::class ,'logout'])->name('logout');


Route::get('/', function () {
    return view('welcome');
});
