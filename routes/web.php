<?php

use Illuminate\Support\Facades\Route;

//MODULE 1: MANAGE USER=======================================================================================================
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserProfileController;

    //Login
Route::get('/login', function () {
    return view('ManageUserUI.Login');
})->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.submit');
    //First Time Login Change Password for Agency Staff
// First-time password routes for agency
Route::get('/first-time-password', [LoginController::class, 'showFirstTimePasswordForm'])->name('first.time.password');
Route::post('/first-time-password', [LoginController::class, 'saveFirstTimePassword'])->name('first.time.password.save');
    //Display Home
Route::get('/home', function () {return view('SharedUI.HomepageUI');})->name('home');
    //Log Out
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    //Public Registration
Route::get('/register', [UserProfileController::class, 'showRegistrationForm'])->name('public.register');
Route::post('/register', [UserProfileController::class, 'store'])->name('public.register.store');
    //View Profile
Route::get('/viewprofile', [UserProfileController::class, 'view'])->name('profile.view');
    //Edit Profile
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [UserProfileController::class, 'view'])->name('view.profile');
    Route::get('/profile/edit', [UserProfileController::class, 'edit'])->name('edit.profile');
    Route::put('/profile/save-edit', [UserProfileController::class, 'saveEdit'])->name('edit.profile.save');
});
    //Update Security
// web.php
Route::get('/update-security', [UserProfileController::class, 'showUpdateSecurityForm'])->name('showUpdateSecurityForm.security');
Route::put('/update-security-save', [UserProfileController::class, 'updateSecurity'])->name('update.security');

//Test
Route::get('', function () {
    return view('ManageUserUI.RegisterAgency');
});

    //Register Agency
Route::post('/register-agency', [UserProfileController::class, 'registerAgency'])->name('register.agency');

//END OF MODULE 1:  MANAGE USER================================================================================================


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
Route::get('/public/list-inquiry', [InquiryController::class, 'p_ListInquiry']);
Route::post('/inquiry/store', [InquiryController::class, 'p_InquiryForm'])->name('inquiry.store');


// Inquiry Assignment
use App\Http\Controllers\InquiryAssignmentController;

Route::get('/public/inquiries', [InquiryAssignmentController::class, 'publicOwnList']);
Route::get('/agency/dashboard', [InquiryAssignmentController::class, 'a_ReviewInquiry'])->name('agency.review.inquiry');
Route::get('/agency/assign', [InquiryAssignmentController::class, 'a_AssignInquiry'])->name('agency.assign.form');
Route::post('/agency/assign', [InquiryAssignmentController::class, 'storeAssignment'])->name('agency.assign.inquiry');
Route::get('/agency/reports', [InquiryAssignmentController::class, 'a_DisplayReport'])->name('agency.display.report');
