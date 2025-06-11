<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrackingProgressController extends Controller
{
    public function home()
    {
        return view('SharedUI.HomepageUI');
    }

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

    public function p_InquiryList()
    {
        return view('InquiryProgressTrackingUI.Public.ListAllInquiryUI');
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
