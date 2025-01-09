<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\Authorization;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->middleware('auth:api')->name('me');
    Route::put('/{id}', [AuthController::class, 'update'])->middleware('auth:api')->name('update')->middleware(Authorization::class . ':admin');
    Route::delete('/{id}', [AuthController::class, 'delete'])->middleware('auth:api')->name('delete')->middleware(Authorization::class . ':admin');
    Route::post('/allUser', [AuthController::class, 'allUser'])->middleware('auth:api')->name('allUser')->middleware(Authorization::class . ':admin');
});

