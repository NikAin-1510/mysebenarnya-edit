<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Inquiry;

class TrackingProgressController extends Controller
{
    public function a_InquiryList()
    {
        return view('InquiryProgressTrackingUI.Agency.ListAssignedInquiryUI');
    }

    public function a_UpdateStatus()
    {
        return view('InquiryProgressTrackingUI.Agency.UpdateStatusUI');
    }

    public function m_CreateReport()
    {
        return view('InquiryProgressTrackingUI.MCMC.CreateReportUI');
    }

    public function m_InquiryDetails()
    {
        return view('InquiryProgressTrackingUI.MCMC.MonitorProgressUI');
    }

    public function m_DisplayReport()
    {
        return view('InquiryProgressTrackingUI.MCMC.DisplayReportUI');
    }

    public function m_InquiryList()
    {
        return view('InquiryProgressTrackingUI.MCMC.ProgListInquiryUI');
    }

    public function p_OwnInquiryDetails()
    {
        return view('InquiryProgressTrackingUI.Public.DetailsOwnInquiryUI');
    }

    public function p_ListAllInquiry(Request $request)
    {
        $status = $request->input('status');
        $ownOnly = $request->has('own_only');
        $publicId = $request->input('public_id'); // Optional, based on checkbox

        $query = Inquiry::with(['progress', 'latestAssignment']);

        // Filter by VerificationStatus or InvestigationBeginDate via relation
        if (!empty($status)) {
            if ($status === 'Under Investigation') {
                $query->whereHas('progress', function ($q) {
                    $q->whereNotNull('InvestigationBeginDate')
                        ->whereNull('VerificationStatus');
                });
            } elseif (in_array($status, ['Verified as True', 'Identified as Fake'])) {
                $query->whereHas('progress', function ($q) use ($status) {
                    $q->where('VerificationStatus', $status);
                });
            }
        }

        // Filter by user's own submissions
        if ($ownOnly && !empty($publicId)) {
            $query->where('PublicID', $publicId);
        }

        $inquiries = $query->orderByDesc('SubmissionDate')->get();

        return view('InquiryProgressTrackingUI.Public.ListAllInquiryUI', compact('inquiries'));
    }


    public function p_NotificationDetails()
    {
        return view('InquiryProgressTrackingUI.Public.NotificationDetailsUI');
    }

    public function p_NotificationList()
    {
        return view('InquiryProgressTrackingUI.Public.NotificationListUI');
    }



    public function p_OwnInquiryList()
    {
        return view('InquiryProgressTrackingUI.Public.OwnListInquiryUI');
    }

    public function p_InquiryDetails()
    {
        return view('InquiryProgressTrackingUI.Public.ProgInquiryDetailsUI');
    }
}
