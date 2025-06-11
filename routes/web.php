<?php

use Illuminate\Support\Facades\Route;

Route::get('/home', function () {
    return view('SharedUI.HomepageUI');
})->name('home');

//Module 1
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserProfileController;

Route::get('/login', function () {
    return view('ManageUserUI.Login');
})->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.submit');

Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [UserProfileController::class, 'showRegistrationForm'])->name('public.register');
Route::post('/register', [UserProfileController::class, 'store'])->name('public.register.store');


// Tracking Progress Controller
use App\Http\Controllers\TrackingProgressController;
// agency
Route::get('/agency/inquirylist', [TrackingProgressController::class, 'a_InquiryList']);
Route::get('/agency/updatestatus', [TrackingProgressController::class, 'a_UpdateStatus']);
// mcmc
Route::get('/mcmc/create-report', [TrackingProgressController::class, 'm_CreateReport']);
Route::get('/mcmc/inquiry-details', [TrackingProgressController::class, 'm_InquiryDetails']);
Route::get('/mcmc/display-report', [TrackingProgressController::class, 'm_DisplayReport']);
Route::get('/mcmc/inquiry-list', [TrackingProgressController::class, 'm_InquiryList']);
// public
Route::get('/public/own-inquiry-details', [TrackingProgressController::class, 'p_OwnInquiryDetails']);
Route::get('/public/inquiry-list', [TrackingProgressController::class, 'p_InquiryList']);
Route::get('/public/notification-details', [TrackingProgressController::class, 'p_NotificationDetails']);
Route::get('/public/notification-list', [TrackingProgressController::class, 'p_NotificationList']);
Route::get('/public/own-inquiry-list', [TrackingProgressController::class, 'p_OwnInquiryList']);
Route::get('/public/inquiry-details', [TrackingProgressController::class, 'p_InquiryDetails']);
