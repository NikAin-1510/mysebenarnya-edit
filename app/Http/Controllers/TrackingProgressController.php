<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class TrackingProgressController extends Controller
{
    public function home()
    {
        return view('SharedUI.HomepageUI');
    }

    // agency
    public function a_InquiryList()
    {
        $loggedInUserID = Auth::user()->UserID;

        // Get the agency that belongs to this user
        $agency = DB::table('agency')->where('UserID', $loggedInUserID)->first();

        if (!$agency) {
            abort(403, 'No agency linked to this account.');
        }

        $agencyName = $agency->AgencyName;

        $assignedInquiries = DB::table('inquiryassignment')
            ->join('inquiry', 'inquiryassignment.InquiryID', '=', 'inquiry.InquiryID')
            ->leftJoin('inquiryprogress', 'inquiry.InquiryID', '=', 'inquiryprogress.InquiryID')
            ->select(
                'inquiryassignment.*',
                'inquiry.InquiryTitle',
                'inquiryprogress.InvestigationBeginDate',
                'inquiryprogress.VerificationStatus'
            )
            ->where('inquiryassignment.AgencyName', $agencyName)
            ->get();

        return view('InquiryProgressTrackingUI.Agency.ListAssignedInquiryUI', [
            'assignedInquiries' => $assignedInquiries
        ]);
    }

    public function a_UpdateStatus()
    {
        return view('InquiryProgressTrackingUI.Agency.UpdateStatusUI');
    }

    //mcmc
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


    //public
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
