<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\DB;

Route::get('/db-test', function () {
    try {
        DB::connection()->getPdo();
        return "✅ Database is connected: " . DB::connection()->getDatabaseName();
    } catch (\Exception $e) {
        return "❌ Could not connect to the database. Error: " . $e->getMessage();
    }
});

use App\Http\Controllers\LoginController;

Route::get('/login', function () {
    return view('login');
})->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.submit');

Route::get('/logout', [LoginController::class, 'logout'])->name('logout');


// Tracking Progress Controller
use App\Http\Controllers\TrackingProgressController;
// home
Route::get('/home', [TrackingProgressController::class, 'home']);
// agency
Route::post('/agency/inquirylist', [TrackingProgressController::class, 'a_InquiryList']);
Route::post('/agency/updatestatus', [TrackingProgressController::class, 'a_UpdateStatus']);
// mcmc
Route::post('/mcmc/create-report', [TrackingProgressController::class, 'm_CreateReport']);
Route::post('/mcmc/inquiry-details', [TrackingProgressController::class, 'm_InquiryDetails']);
Route::post('/mcmc/display-report', [TrackingProgressController::class, 'm_DisplayReport']);
Route::post('/mcmc/inquiry-list', [TrackingProgressController::class, 'm_InquiryList']);
// public
Route::post('/public/own-inquiry-details', [TrackingProgressController::class, 'p_OwnInquiryDetails']);
Route::post('/public/inquiry-list', [TrackingProgressController::class, 'p_InquiryList']);
Route::post('/public/notification-details', [TrackingProgressController::class, 'p_NotificationDetails']);
Route::post('/public/notification-list', [TrackingProgressController::class, 'p_NotificationList']);
Route::post('/public/own-inquiry-list', [TrackingProgressController::class, 'p_OwnInquiryList']);
Route::post('/public/inquiry-details', [TrackingProgressController::class, 'p_InquiryDetails']);
