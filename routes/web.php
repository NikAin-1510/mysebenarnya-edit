<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
Route::get('/login', function () {
    return view('login');
})->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.submit');

Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
