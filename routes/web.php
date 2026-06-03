<?php

use Illuminate\Support\Facades\Route;

//Module1: Manage User
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserProfileController;

Route::get('/', function () {
    return redirect()->route('login');
});

//1.Login
Route::get('/login', function () {
    return view('ManageUserUI.Login');
})->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.submit');
//2.First Time Login Change Password for Agency Staff
Route::get('/first-time-password', [LoginController::class, 'showFirstTimePasswordForm'])->name('first.time.password');
Route::post('/first-time-password', [LoginController::class, 'saveFirstTimePassword'])->name('first.time.password.save');
//3.Public Registration
Route::get('/register', [UserProfileController::class, 'showRegistrationForm'])->name('public.register');
Route::post('/register', [UserProfileController::class, 'store'])->name('public.register.store');

Route::middleware(['auth'])->group(function () {
    //4.Display Home
    Route::get('/home', [LoginController::class, 'displayHome'])->name('display.home');
    //5.Log Out
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');


    //6.View Profile
    Route::get('/profile', [UserProfileController::class, 'view'])->name('view.profile');
    //Edit Profile
    Route::get('/profile/edit', [UserProfileController::class, 'edit'])->name('edit.profile');
    Route::put('/profile/save-edit', [UserProfileController::class, 'saveEdit'])->name('edit.profile.save');



    // Inquiry Form Submission
    //7.Update Security
    Route::get('/update-security', [UserProfileController::class, 'showUpdateSecurityForm'])->name('showUpdateSecurityForm.security');
    Route::put('/update-security-save', [UserProfileController::class, 'updateSecurity'])->name('update.security');
    //8.Register Agency
    Route::get('/register-agency', [UserProfileController::class, 'showRegisterAgency'])->name('show.register.agency');
    Route::post('/register-agency', [UserProfileController::class, 'registerAgency'])->name('register.agency');
    //9.View Users List
    Route::get('/userlist', [UserProfileController::class, 'viewAllUsers'])->name('view.all.users');
    //10.View User Data
    Route::get('/users/{id}/view', [UserProfileController::class, 'viewUserData'])->name('view.user.data');
    //11.ReportDashboard
    Route::get('/report/dashboard', [UserProfileController::class, 'displayReportDashboard'])->name('show.reportDashboard');
    //12.UserReports
    Route::get('/user-report', [UserProfileController::class, 'showUserReport'])->name('show.registeredUserReport');
    Route::get('/user-report/pdf', [UserProfileController::class, 'downloadPdf'])->name('registeredUserReport.download');
    Route::get('/user-report/excel', [UserProfileController::class, 'downloadExcel'])->name('registeredUserReport.downloadExcel');
});
//Module1: Manage User





//Module2: ModuleInquiry Form Submission
use App\Http\Controllers\InquiryController;
// agency
Route::get('/agency/review-inquiry', [InquiryController::class, 'a_ReviewInquiry']);
Route::get('/agency/list-assigned-inquiry', [InquiryController::class, 'a_ListAssignedInquiry'])->name('agency.list.assigned');
Route::get('/agency/inquiry/review/{id}', [InquiryController::class, 'a_ReviewInquiry'])->name('agency.review.inquiry');


// mcmc
Route::get('/previous-inquiries/download', [InquiryController::class, 'downloadFilteredInquiries'])->name('inquiries.download');
Route::get('/display-report', [InquiryController::class, 'm_DisplayReport'])->name('show.totalInquiryReport');
Route::get('/mcmc/export/report/pdf', [InquiryController::class, 'exportReportToPDF'])->name('mcmc.export.pdf');
Route::get('/mcmc/export/report/excel', [InquiryController::class, 'exportReportToExcel'])->name('mcmc.export.excel');


Route::get('/mcmc/inquiry/{id}', [InquiryController::class, 'm_DetailsInquiry'])->name('inquiry.own.view');
Route::put('/mcmc/inquiry/{id}/update-category', [InquiryController::class, 'updateCategory'])->name('mcmc.update.category');
Route::get('/mcmc/list-new-inquiry', [InquiryController::class, 'm_ListInquiry'])->name('mcmc.new.inquiry');

Route::get('/mcmc/allinquiry', [InquiryController::class, 'm_ListAllInquiry'])->name('mcmc.all.inquiry');
Route::get('/mcmc/inquiry/details/{id}', [InquiryController::class, 'm_AllDetailsInquiry'])->name('mcmc.all.details');




// public
Route::get('/public/list-own-inquiries', [InquiryController::class, 'p_ListOwnInquiries'])->name('list.own.inquiries');
Route::get('/public/details-own-inquiry/{id}', [InquiryController::class, 'p_DetailsOwnInquiry'])->name('details.own.inquiry');
Route::get('/public/list-inquiry', [InquiryController::class, 'p_ListInquiry']);
Route::post('/complaint/store', [InquiryController::class, 'store'])->name('complaint.store');
Route::get('/public/details-all-inquiry/{id}', [InquiryController::class, 'p_DetailsAllInquiry'])->name('details.all.inquiry');
Route::get('/inquiry/assigned-agency/{id}', [InquiryController::class, 'p_ViewAssignedAgency'])->name('inquiry.assigned.agency');

Route::get('/inquiry/form', [InquiryController::class, 'create'])->name('inquiry.form');
// Handle form submission (POST)
Route::post('/inquiry/submit', [InquiryController::class, 'store'])->name('inquiry.submit');
Route::get('/inquiries/{id}/view', [InquiryController::class, 'view'])->name('inquiry.view');
Route::post('/inquiry/store', [InquiryController::class, 'p_InquiryForm'])->name('inquiry.store');





//Module3: Inquiry Assignment
use App\Http\Controllers\InquiryAssignmentController;

Route::get('/public/inquiries', [InquiryAssignmentController::class, 'publicOwnList'])
    ->middleware('auth') // ← this is needed
    ->name('public.list');
//MCMC
// Assign Inquiry (form display)

Route::get('/mcmc/inquiry/assign/{id}', [InquiryAssignmentController::class, 'showAssignForm'])->name('mcmc.assign.form');

// Assign Inquiry (handle form submit)

Route::post('/mcmc/inquiry/assign/{id}', [InquiryAssignmentController::class, 'storeAssignment'])->name('mcmc.assign.inquiry');
Route::get('/mcmc/assigned/{id}', [InquiryAssignmentController::class, 'm_ReviewInquiry'])->name('mcmc.review.inquiry');

Route::get('/mcmc/report', [InquiryAssignmentController::class, 'showInquiryReport'])->name('mcmc.report');
Route::get('/mcmc/report/export/pdf', [InquiryAssignmentController::class, 'exportInquiryReportPDF'])->name('mcmc.report.export.pdf');
Route::get('/mcmc/report/export/excel', [InquiryAssignmentController::class, 'exportInquiryReportExcel'])->name('mcmc.report.export.excel');

//AGENCY
Route::get('/agency/inquiry-list', [InquiryAssignmentController::class, 'a_ListAssignedInquiry'])->name('agency.inquirylist');
Route::get('/agency/inquirylist/{id}', [InquiryAssignmentController::class, 'a_InquiryDetails'])->name('agency.inquiry.details');
Route::post('/agency/inquirylist/{id}/action', [InquiryAssignmentController::class, 'handleAction'])->name('agency.inquiry.action');




//Module4: Tracking Progress Controller
use App\Http\Controllers\TrackingProgressController;
// agency
Route::get('/agency/inquirylist', [TrackingProgressController::class, 'a_InquiryList']);
Route::get('/agency/updatestatus', [TrackingProgressController::class, 'a_UpdateStatus'])->name('progress.update.status');
Route::post('/agency/updatestatus/save', [TrackingProgressController::class, 'a_SaveStatus']);
Route::post('/agency/notify-mcmc', [TrackingProgressController::class, 'a_NotifyMCMC']);
Route::post('/agency/request-reassignment', [TrackingProgressController::class, 'a_RequestReassignment']);
// mcmc
Route::get('/mcmc/inquiry-progress', [TrackingProgressController::class, 'm_InquiryProgress'])->name('monitor.progress');
Route::get('/mcmc/progress-doc/{statusID}', [TrackingProgressController::class, 'm_SupportingDoc'])->name('progress.view.pdf');
//mcmc report
Route::get('/mcmc/display-report', [TrackingProgressController::class, 'm_DisplayReport'])->name('show.agencyPerformanceReport');
Route::post('/mcmc/reports', [TrackingProgressController::class, 'm_GenerateReport'])->name('mcmc.reports.generate');
Route::get('/mcmc/reports/excel', [TrackingProgressController::class, 'm_ExportExcel'])->name('mcmc.reports.excel');
Route::get('/mcmc/reports/pdf', [TrackingProgressController::class, 'm_ExportPDF'])->name('mcmc.reports.pdf');
// public
Route::get('/public/own-inquiry-details', [TrackingProgressController::class, 'p_ProgOwnInquiry']);
Route::get('/public/notification-details', [TrackingProgressController::class, 'p_NotificationDetails']);
Route::get('/public/notification-list', [TrackingProgressController::class, 'p_NotificationList'])->name('notification.list');
Route::get('/public/inquiry-details', [TrackingProgressController::class, 'p_ProgAllInquiry']);
Route::get('/public/inquiry-list', [TrackingProgressController::class, 'p_ListAllInquiry'])->name('public.all.list');


//bukan faten punya
Route::get('own-inquiry-details', [TrackingProgressController::class, 'p_OwnInquiryList'])
    ->middleware('auth')  // assuming login required
    ->name('inquiry.own.list');
