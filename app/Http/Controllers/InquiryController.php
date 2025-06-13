<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inquiry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


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
        $inquiry->InquiryID = uniqid('INQ');
        $inquiry->UserID = Auth::id(); // Assuming user is logged in
        $inquiry->Title = $request->input('title');
        $inquiry->Description = $request->input('description');
        $inquiry->URL = $request->input('url');
        $inquiry->Status = 'Submitted';
        $inquiry->DateSubmitted = now();

        // Handle file upload
        if ($request->hasFile('evidence')) {
            $file = $request->file('evidence');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('evidence', $filename, 'public'); // save in storage/app/public/evidence
            $inquiry->EvidencePath = $path;
        }

        $inquiry->save();

        return redirect()->back()->with('success', 'Inquiry submitted successfully.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'url' => 'required|url',
            'evidence' => 'nullable|file|max:5120', // 5MB
        ]);

        $inquiry = new Inquiry();
        $inquiry->title = $request->title;
        $inquiry->description = $request->description;
        $inquiry->url = $request->url;

        if ($request->hasFile('evidence')) {
            $file = $request->file('evidence');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('evidence', $filename, 'public');
            $inquiry->evidence = $filePath;
        }

        $inquiry->status = 'Submitted';
        $inquiry->date = now();
        $inquiry->save();

        return response()->json(['message' => 'Inquiry stored successfully.'], 200);
    }
}
