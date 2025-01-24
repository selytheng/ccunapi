<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\YearController;
use App\Http\Controllers\EventController;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\Authorization;
use App\Http\Controllers\FeedbackController;

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
    Route::post('/changepassword', [AuthController::class, 'selfChangePassword'])->middleware('auth:api')->name('changepassword');
    Route::post('/changeinfo', [AuthController::class, 'selfChangeInfo'])->middleware('auth:api')->name('changeinfo');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'partners'
],  function () {
    Route::post('/', [PartnerController::class, 'create'])->middleware(Authorization::class . ':admin,partner');
    Route::get('/', [PartnerController::class, 'get']);
    Route::get('/{id}', [PartnerController::class, 'getById']);
    Route::patch('/{id}', [PartnerController::class, 'update'])->middleware(Authorization::class . ':admin,partner');
    Route::delete('/{id}', [PartnerController::class, 'delete'])->middleware(Authorization::class . ':admin,partner');
    Route::get('/{id}/majors', [PartnerController::class, 'getAllMajorInPartner']);
    Route::get('/{id}/courses', [PartnerController::class, 'getAllCoursesInPartner']);
    Route::get('/{id}/events', [PartnerController::class, 'getAllEventsInPartner']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'majors'
],  function () {
    Route::get('/years', [YearController::class, 'getAll']);
    Route::post('/', [MajorController::class, 'create'])->middleware(Authorization::class . ':admin,partner');
    Route::get('/', [MajorController::class, 'get']);
    Route::get('/{id}', [MajorController::class, 'getById']);
    Route::patch('/{id}', [MajorController::class, 'update'])->middleware(Authorization::class . ':admin,partner');
    Route::delete('/{id}', [MajorController::class, 'delete'])->middleware(Authorization::class . ':admin,partner');
    Route::get('/{id}/courses', [MajorController::class, 'getAllCoursesInMajor']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'courses'
], function () {
    Route::post('/', [CourseController::class, 'create'])->middleware(Authorization::class . ':admin,partner');
    Route::get('/', [CourseController::class, 'get']);
    Route::get('/all', [CourseController::class, 'getAllCourses']);
    Route::get('/{id}', [CourseController::class, 'getById']);
    Route::put('/{id}', [CourseController::class, 'update'])->middleware(Authorization::class . ':admin,partner');
    Route::delete('/{id}', [CourseController::class, 'delete'])->middleware(Authorization::class . ':admin,partner');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'events'
], function () {
    Route::post('/', [EventController::class, 'create'])->middleware(Authorization::class . ':admin,partner');
    Route::get('/', [EventController::class, 'get']);
    Route::get('/{id}', [EventController::class, 'getById']);
    Route::put('/{id}', [EventController::class, 'update'])->middleware(Authorization::class . ':admin,partner');
    Route::delete('/{id}', [EventController::class, 'delete'])->middleware(Authorization::class . ':admin,partner');
    Route::put('/{id}/deletegallery', [EventController::class, 'removeGalleryImages'])->middleware(Authorization::class . ':admin,partner'); // New Route
    Route::put('/{id}/addgallery', [EventController::class, 'addGalleryImages'])->middleware(Authorization::class . ':admin,partner'); // New Route
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'feedback'
], function () {
    Route::post('/', [FeedbackController::class, 'createFeedback']);
    Route::get('/partner/{id}', [FeedbackController::class, 'getFeedbackByPartnerId'])->middleware(Authorization::class . ':admin,partner');
    Route::get('/{id}', [FeedbackController::class, 'getFeedbackById'])->middleware(Authorization::class . ':admin,partner');
});
