<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;

Route::get('/login', function () {
    return view('login');
})->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.submit');

Route::get('/logout', [LoginController::class, 'logout'])->name('logout');


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

// Inquiry Form Submission
use App\Http\Controllers\InquiryController;
// agency
Route::get('/agency/list-assigned-inquiry', [InquiryController::class, 'a_ListAssignedInquiry']);
Route::get('/agency/history-inquiry', [InquiryController::class, 'a_HistoryInquiry']);
Route::get('/agency/review-inquiry', [InquiryController::class, 'a_ReviewInquiry']);
Route::get('/agency/display-report', [InquiryController::class, 'a_DisplayReport']);

// mcmc
Route::get('/mcmc/details-inquiry', [InquiryController::class, 'm_DetailsInquiry']);
Route::get('/mcmc/filtered-inquiry', [InquiryController::class, 'm_FilteredInquiry']);
Route::get('/mcmc/review-inquiry', [InquiryController::class, 'm_ReviewInquiry']);
Route::get('/mcmc/list-inquiry', [InquiryController::class, 'm_ListInquiry']);

// public
Route::get('/public/details-own-inquiry', [InquiryController::class, 'p_DetailsOwnInquiry']);
Route::get('/public/inquiry-form', [InquiryController::class, 'p_InquiryForm']);
Route::get('/public/list-inquiry', [InquiryController::class, 'p_ListInquiry']);

Route::post('/submit-complaint', [InquiryController::class, 'store'])->name('complaint.store');
Route::get('/public-dashboard', [InquiryController::class, 'dashboard'])->name('public.dashboard');


// Inquiry Assignment
use App\Http\Controllers\InquiryAssignmentController;

Route::get('/public/inquiries', [InquiryAssignmentController::class, 'publicOwnList']);
