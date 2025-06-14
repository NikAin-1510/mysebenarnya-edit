<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TrackingProgressController extends Controller
{

    // agency
    public function a_InquiryList() //ainul kalau nak amik
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

    public function a_UpdateStatus(Request $request)
    {
        $inquiryID = $request->query('id');
        $userID = Auth::user()->UserID;

        // Get agency info
        $agency = DB::table('agency')->where('UserID', $userID)->first();

        // Get progress details
        $inquiryProgress = DB::table('inquiryprogress')
            ->where('InquiryID', $inquiryID)
            ->where('AgencyID', $agency->AgencyID)
            ->first();

        return view('InquiryProgressTrackingUI.Agency.UpdateStatusUI', [
            'inquiryID' => $inquiryID,
            'agency' => $agency,
            'progress' => $inquiryProgress
        ]);
    }

    public function a_SaveStatus(Request $request)
    {
        $request->validate([
            'InquiryID' => 'required',
            'AgencyID' => 'required',
            'VerificationStatus' => 'required',
            'InvestigationDetails' => 'nullable|string',
            'InvestigationDoc' => 'nullable|file|max:2048',
        ]);

        $data = [
            'InquiryID' => $request->InquiryID,
            'AgencyID' => $request->AgencyID,
            'VerificationStatus' => $request->VerificationStatus,
            'InvestigationDetails' => $request->InvestigationDetails,
            'VerificationDateTime' => now(),
        ];

        if ($request->hasFile('InvestigationDoc')) {
            $doc = $request->file('InvestigationDoc')->get();
            $data['InvestigationDoc'] = $doc;
        }

        DB::table('inquiryprogress')->updateOrInsert(
            ['InquiryID' => $request->InquiryID, 'AgencyID' => $request->AgencyID],
            $data
        );

        return redirect()->back()->with('success', 'Status updated successfully.');
    }


    //mcmc
    public function m_CreateReport()
    {
        return view('SharedUI.CreateReportUI');
    }

    public function m_InquiryDetails()
    {
        return view('InquiryProgressTrackingUI.MCMC.MonitorProgressUI');
    }

    public function m_DisplayReport()
    {
        return view('InquiryProgressTrackingUI.MCMC.ProgDisplayReportUI');
    }


    //public
    public function p_OwnInquiryDetails()
    {
        return view('InquiryProgressTrackingUI.Public.ProgOwnInquiryUI');
    }

    public function p_NotificationDetails()
    {
        return view('InquiryProgressTrackingUI.Public.NotificationDetailsUI');
    }

    public function p_NotificationList()
    {
        return view('InquiryProgressTrackingUI.Public.NotificationListUI');
    }

    public function p_InquiryDetails()
    {
        return view('InquiryProgressTrackingUI.Public.ProgAllInquiryUI');
    }
}
