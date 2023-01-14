<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;


Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login')->name('login');
    Route::post('register', 'register')->name('register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

Route::controller(FileController::class)->group(function () {
    Route::get('/files', 'index')->name('index');
    Route::post('/files', 'store')->name('store');
});