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
Route::get('/agency/review-inquiry', [InquiryController::class, 'a_ReviewInquiry']);
Route::get('/agency/list-assigned-inquiry', [InquiryController::class, 'a_ListAssignedInquiry'])->name('agency.list.assigned');
Route::get('/agency/inquiry/review/{id}', [InquiryController::class, 'a_ReviewInquiry'])->name('agency.review.inquiry');


// mcmc
Route::get('/mcmc/details-inquiry', [InquiryController::class, 'm_DetailsInquiry']);
Route::get('/mcmc/list-inquiry', [InquiryController::class, 'm_ListInquiry'])->name('mcmc.inquiries');
Route::get('/previous-inquiries/download', [InquiryController::class, 'downloadFilteredInquiries'])->name('inquiries.download');
Route::get('/display-report', [InquiryController::class, 'm_DisplayReport'])->name('mcmc.display.report');
Route::get('/mcmc/export/report/pdf', [InquiryController::class, 'exportReportToPDF'])->name('mcmc.export.pdf');
Route::get('/mcmc/export/report/excel', [InquiryController::class, 'exportReportToExcel'])->name('mcmc.export.excel');
Route::get('/mcmc/inquiry/{id}', [InquiryController::class, 'm_DetailsInquiry'])->name('inquiry.view');
Route::put('/inquiry/update-category/{id}', [InquiryController::class, 'UpdateCategory'])->name('inquiry.update.category');
Route::get('/mcmc/allinquiry', [InquiryController::class, 'm_ListAllInquiry'])->name('mcmc.all.inquiry');


// public
Route::get('/public/details-own-inquiry', [InquiryController::class, 'p_DetailsOwnInquiry']);

Route::get('/public/list-inquiry', [InquiryController::class, 'p_ListInquiry']);
Route::post('/complaint/store', [InquiryController::class, 'store'])->name('complaint.store');
// Show the public inquiry form (GET)

// Show the form (GET)
Route::get('/inquiry/form', [InquiryController::class, 'create'])->name('inquiry.form');
// Handle form submission (POST)
Route::post('/inquiry/submit', [InquiryController::class, 'store'])->name('inquiry.submit');

Route::get('/inquiries/{id}/view', [InquiryController::class, 'view'])->name('inquiry.view');

Route::get('/public/details-inquiry', [InquiryController::class, 'P_DetailsOwnInquiry'])->name('public.details.inquiry');



// Inquiry Assignment
use App\Http\Controllers\InquiryAssignmentController;

Route::get('/public/inquiries', [InquiryAssignmentController::class, 'publicOwnList']);
Route::get('/agency/dashboard', [InquiryAssignmentController::class, 'a_ReviewInquiry'])->name('agency.review.inquiry');
Route::get('/agency/assign', [InquiryAssignmentController::class, 'a_AssignInquiry'])->name('agency.assign.form');
Route::post('/agency/assign', [InquiryAssignmentController::class, 'storeAssignment'])->name('agency.assign.inquiry');
Route::get('/agency/reports', [InquiryAssignmentController::class, 'a_DisplayReport'])->name('agency.display.report');
Route::get('/agency/inquiries', [InquiryAssignmentController::class, 'a_ListAssignedInquiry'])->name('agency.inquiries');
Route::get('/agency/inquiries/{id}', [InquiryAssignmentController::class, 'a_InquiryDetails'])->name('agency.inquiry.details');
Route::post('/agency/inquiries/{id}/action', [InquiryAssignmentController::class, 'handleAction'])->name('agency.inquiry.action');
Route::get('/inquiry/assign-agency/{id}', [InquiryController::class, 'mcmc_AssignAgencyForm'])->name('inquiry.assign.agency');
Route::post('/inquiry/assign-agency/{id}', [InquiryController::class, 'mcmc_AssignAgencySubmit'])->name('inquiry.assign.agency.submit');
