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
    Route::get('/files/allsize', 'getDiskSize')->name('allsize');
    Route::get('/files/directorysize', 'getDirectorySize')->name('directorysize');
    Route::get('/files/{file_name}', 'download')->name('download');
    Route::post('/files', 'store')->name('store');
    Route::put('/files/{file_name}', 'update')->name('rename');
    Route::delete('/files/{file_name}', 'destroy')->name('destroy');
    Route::post('/files/createdirectory', 'createDirectory')->name('createdirectory');
});