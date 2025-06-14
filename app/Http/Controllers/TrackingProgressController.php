<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            'VerificationDateTime' => now(), // always updated
        ];

        // Only set InvestigationBeginDate if status is 'Under Investigation'
        if ($request->VerificationStatus === 'Under Investigation' && empty($existing?->InvestigationBeginDate)) {
            $data['InvestigationBeginDate'] = now();
        }

        // Handle file upload
        if ($request->hasFile('InvestigationDoc')) {
            $filename = $request->file('InvestigationDoc')->store('investigation_docs', 'public');
            $data['InvestigationDoc'] = $filename;
        }

        // Check if record exists
        $existing = DB::table('inquiryprogress')
            ->where('InquiryID', $request->InquiryID)
            ->where('AgencyID', $request->AgencyID)
            ->first();

        if ($existing) {
            // ✅ Update existing progress
            DB::table('inquiryprogress')
                ->where('InquiryID', $request->InquiryID)
                ->where('AgencyID', $request->AgencyID)
                ->update($data);
        } else {
            // ✅ Insert new record with generated StatusID
            $data['StatusID'] = 'ST' . strtoupper(Str::random(6));
            DB::table('inquiryprogress')->insert($data);
        }

        return redirect()->route('agency.assign.form')->with('success', 'Inquiry updated successfully.');
    }

    public function a_NotifyMCMC(Request $request)
    {
        $inquiryID = $request->InquiryID;
        // Logic to send MCMC notification or update DB flag
        return redirect()->back()->with('success', 'MCMC has been notified.');
    }

    public function a_RequestReassignment(Request $request)
    {
        $inquiryID = $request->InquiryID;
        $userID = Auth::user()->UserID;

        $agency = DB::table('agency')->where('UserID', $userID)->first();

        // Update the reassignment request flag
        DB::table('inquiryprogress')
            ->where('InquiryID', $inquiryID)
            ->where('AgencyID', $agency->AgencyID)
            ->update(['ReassignmentRequested' => true]);

        return redirect()->back()->with('success', 'Reassignment requested.');
    }

    //mcmc
    public function m_CreateReport()
    {
        return view('SharedUI.CreateReportUI');
    }


    public function m_InquiryProgress(Request $request)
    {
        $inquiryID = $request->query('id');

        $inquiry = DB::table('inquiry')->where('InquiryID', $inquiryID)->first();

        $progressList = DB::table('inquiryprogress')
            ->join('agency', 'inquiryprogress.AgencyID', '=', 'agency.AgencyID')
            ->where('inquiryprogress.InquiryID', $inquiryID)
            ->select('inquiryprogress.*', 'agency.AgencyName')
            ->get();

        return view('InquiryProgressTrackingUI.MCMC.MonitorProgressUI', [
            'inquiry' => $inquiry,
            'progressList' => $progressList
        ]);
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
