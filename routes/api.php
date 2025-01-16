<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\YearController;
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
    Route::put('/{id}', [AuthController::class, 'update'])->middleware('auth:api')->name('update');
    Route::delete('/{id}', [AuthController::class, 'delete'])->middleware('auth:api')->name('delete');
    Route::post('/allUser', [AuthController::class, 'allUser'])->middleware('auth:api')->name('allUser');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'partners'
],  function () {
    Route::post('/', [PartnerController::class, 'create'])->middleware(Authorization::class . ':admin,partner');
    Route::get('/', [PartnerController::class, 'get'])->middleware(Authorization::class . ':admin,partner');
    Route::get('/{id}', [PartnerController::class, 'getById'])->middleware(Authorization::class . ':admin,partner');
    Route::patch('/{id}', [PartnerController::class, 'update'])->middleware(Authorization::class . ':admin,partner');
    Route::delete('/{id}', [PartnerController::class, 'delete'])->middleware(Authorization::class . ':admin,partner');
    Route::get('/{id}/majors', [PartnerController::class, 'getAllMajorInPartner'])->middleware(Authorization::class . ':admin,partner');
    Route::get('/{id}/courses', [PartnerController::class, 'getAllCoursesInPartner'])->middleware(Authorization::class . ':admin,partner');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'majors'
],  function () {
    Route::get('/years', [YearController::class, 'getAll']);
    Route::post('/', [MajorController::class, 'create'])->middleware(Authorization::class . ':admin,partner');
    Route::get('/', [MajorController::class, 'get'])->middleware(Authorization::class . ':admin,partner');
    Route::get('/{id}', [MajorController::class, 'getById'])->middleware(Authorization::class . ':admin,partner');
    Route::patch('/{id}', [MajorController::class, 'update'])->middleware(Authorization::class . ':admin,partner');
    Route::delete('/{id}', [MajorController::class, 'delete'])->middleware(Authorization::class . ':admin,partner');
    Route::get('/{id}/courses', [MajorController::class, 'getAllCoursesInMajor'])->middleware(Authorization::class . ':admin,partner');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'courses'
], function () {
    Route::post('/', [CourseController::class, 'create'])->middleware(Authorization::class . ':admin,partner');
    Route::get('/', [CourseController::class, 'get'])->middleware(Authorization::class . ':admin,partner');
    Route::get('/all', [CourseController::class, 'getAllCourses'])->middleware(Authorization::class . ':admin,partner');
    Route::get('/{id}', [CourseController::class, 'getById'])->middleware(Authorization::class . ':admin,partner');
    Route::put('/{id}', [CourseController::class, 'update'])->middleware(Authorization::class . ':admin,partner');
    Route::delete('/{id}', [CourseController::class, 'delete'])->middleware(Authorization::class . ':admin,partner');
});
