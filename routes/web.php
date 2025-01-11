<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});
Route::get('/signin', function () {
    return view('login');
});
Route::get('/signup', function () {
    return view('register');
});
Route::get('/loginsuccess', function () {
    return view('loginsuccess');
});

