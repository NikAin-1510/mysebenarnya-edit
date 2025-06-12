<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inquiry;
use Illuminate\Support\Facades\Auth;



class InquiryController extends Controller
{
    // === AGENCY ===
    public function a_DisplayReport()
    {
        return view('InquiryFormSubmissionUI.Agency.DisplayReportUI');
    }

    public function a_HistoryInquiry()
    {
        return view('InquiryFormSubmissionUI.Agency.HistoryInquiryUI');
    }

    public function a_ListAssignedInquiry()
    {
        return view('InquiryFormSubmissionUI.Agency.ListAssignedInquiryUI');
    }

    public function a_ReviewInquiry()
    {
        return view('InquiryFormSubmissionUI.Agency.ReviewInquiryUI');
    }

    // === MCMC ===
    public function m_ListInquiry()
    {
        return view('InquiryFormSubmissionUI.MCMC.ListInquiryUI');
    }

    public function m_FilteredInquiry()
    {
        return view('InquiryFormSubmissionUI.MCMC.FilteredInquiryUI'); // fix path if needed
    }

    public function m_DetailsInquiry()
    {
        return view('InquiryFormSubmissionUI.MCMC.DetailsInquiryUI'); // fix if reused
    }

    public function m_ReviewInquiry()
    {
        return view('InquiryFormSubmissiontUI.MCMC.ReviewInquiryUI');
    }

    // === PUBLIC ===
    public function p_ListInquiry()
    {
        return view('InquiryFormSubmissionUI.Public.ListInquiryUI');
    }

    public function p_DetailsOwnInquiry()
    {
        return view('InquiryFormSubmissionUI.Public.DetailsOwnInquiryUI');
    }

    public function p_InquiryForm(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'url' => 'required|url',
            'evidence' => 'nullable|file|max:5120', // 5MB
        ]);

        $inquiry = new Inquiry();
        $inquiry->InquiryID = uniqid('INQ'); // or generate based on your logic
        $inquiry->PublicID = Auth::check() ? Auth::user()->id : 'PUB001';
        $inquiry->InquiryTitle = $request->title;
        $inquiry->InquiryDescription = $request->description;
        $inquiry->SubmissionLink = $request->url;

        if ($request->hasFile('evidence')) {
            $path = $request->file('evidence')->store('evidence', 'public');
            $inquiry->SubmissionEvidence = $path;
        }

        $inquiry->SubmissionStatus = 'Submitted';
        $inquiry->save();


        use App\Http\Controllers\InquiryAssignmentController;

Route::get('/inquiry-form', [InquiryAssignmentController::class, 'showInquiryForm'])->name('inquiry.form');
Route::post('/submit-inquiry', [InquiryAssignmentController::class, 'submitInquiry'])->name('inquiry.submit');

    }

    public function showInquiryForm()
    {
        return view('InquiryFormSubmissionUI.Public.InquiryFormUI');
    }
}
