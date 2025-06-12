<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InquiryController extends Controller
{
    // === AGENCY ===
    public function a_DisplayReport()
    {
        return view('InquiryAssignmentUI.Agency.DisplayReportUI');
    }

    public function a_HistoryInquiry()
    {
        return view('InquiryAssignmentUI.Agency.HistoryInquiryUI');
    }

    public function a_ListAssignedInquiry()
    {
        return view('InquiryAssignmentUI.Agency.ListAssignedInquiryUI');
    }

    public function a_ReviewInquiry()
    {
        return view('InquiryAssignmentUI.Agency.ReviewInquiryUI');
    }

    // === MCMC ===
    public function m_ListInquiry()
    {
        return view('InquiryAssignmentUI.MCMC.ListInquiryUI');
    }

    public function m_FilteredInquiry()
    {
        return view('InquiryAssignmentUI.MCMC.FilteredInquiryUI'); // fix path if needed
    }

    public function m_DetailsInquiry()
    {
        return view('InquiryAssignmentUI.MCMC.DetailsInquiryUI'); // fix if reused
    }

    public function m_ReviewInquiry()
    {
        return view('InquiryAssignmentUI.MCMC.ReviewInquiryUI');
    }

    // === PUBLIC ===
    public function p_ListInquiry()
    {
        return view('InquiryAssignmentUI.Public.ListInquiryUI');
    }

    public function p_DetailsOwnInquiry()
    {
        return view('InquiryAssignmentUI.Public.DetailsOwnInquiryUI');
    }

    public function p_InquiryForm()
    {
        return view('InquiryAssignmentUI.Public.InquiryFormUI');
    }
}
