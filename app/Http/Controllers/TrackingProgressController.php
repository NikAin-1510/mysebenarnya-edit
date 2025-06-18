<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TrackingProgressController extends Controller
{

    // agency
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
        $request->validate([
            'InquiryID' => 'required',
            'Notify' => 'required|in:Further clarification needed,Inquiry is completed,Reassignment requested',
        ]);

        $userID = Auth::user()->UserID;

        $agency = DB::table('agency')->where('UserID', $userID)->first();

        DB::table('inquiryprogress')
            ->where('InquiryID', $request->InquiryID)
            ->where('AgencyID', $agency->AgencyID)
            ->update([
                'Notify' => $request->Notify,
            ]);

        return redirect()->back()->with('success', 'MCMC has been notified: ' . $request->Notify);
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
    public function p_OwnInquiryProg(Request $request)
    {
        $inquiryID = $request->query('id'); // ?id=IQ000123

        if (!$inquiryID) {
            abort(404, 'Inquiry ID not provided.');
        }

        $userID = Auth::user()->UserID;

        // Make sure this inquiry belongs to the current public user
        $inquiry = DB::table('inquiry')
            ->where('InquiryID', $inquiryID)
            ->where('PublicID', $userID)
            ->first();

        if (!$inquiry) {
            abort(403, 'You are not authorized to view this inquiry.');
        }

        // Get progress list for this inquiry
        $progressList = DB::table('inquiryprogress')
            ->where('InquiryID', $inquiryID)
            ->get();

        return view('InquiryProgressTrackingUI.Public.ProgOwnInquiryUI', [
            'inquiry' => $inquiry,
            'progressList' => $progressList,
            'message' => $progressList->isEmpty() ? 'No agencies have submitted progress yet for this inquiry.' : null
        ]);
    }

    public function p_NotificationDetails(Request $request)
    {
        $inquiryID = $request->query('id');
        $status = $request->query('status');
        $userID = Auth::user()->UserID;

        // Ensure the inquiry belongs to this public user
        $inquiry = DB::table('inquiry')
            ->join('publicuser', 'inquiry.PublicID', '=', 'publicuser.PublicID')
            ->where('inquiry.InquiryID', $inquiryID)
            ->where('publicuser.UserID', $userID)
            ->select('inquiry.*')
            ->first();

        if (!$inquiry) {
            abort(403, 'Unauthorized or invalid inquiry.');
        }

        // Get exact match for progress by status
        $progressQuery = DB::table('inquiryprogress')
            ->where('InquiryID', $inquiryID);

        // Only allow these statuses
        $validStatuses = ['Under Investigation', 'Rejected', 'Verified as True', 'Identified as Fake'];

        if (!in_array($status, $validStatuses)) {
            abort(400, 'Invalid status type.');
        }

        // Strict status matching
        if ($status === 'Under Investigation') {
            $progressQuery->whereNotNull('InvestigationBeginDate')
                ->orderByDesc('InvestigationBeginDate');
        } else {
            $progressQuery->where('VerificationStatus', $status)
                ->orderByDesc('VerificationDateTime');
        }

        $latestProgress = $progressQuery->first();

        // Force override fields to match view expectations
        if ($latestProgress) {
            if ($status === 'Rejected') {
                $latestProgress->InvestigationBeginDate = null;
            } elseif ($status === 'Under Investigation') {
                $latestProgress->VerificationDateTime = null;
            }
        }

        return view('InquiryProgressTrackingUI.Public.NotificationDetailsUI', [
            'inquiry' => $inquiry,
            'latestProgress' => $latestProgress,
            'error' => !$latestProgress ? 'No matching progress record found for this status.' : null
        ]);
    }


    public function p_NotificationList()
    {
        $userID = Auth::user()->UserID;

        // Get all inquiry IDs submitted by this user
        $inquiryIDs = DB::table('inquiry')
            ->join('publicuser', 'inquiry.PublicID', '=', 'publicuser.PublicID')
            ->where('publicuser.UserID', $userID)
            ->pluck('inquiry.InquiryID');

        // Get all progress notifications related to those inquiries
        $notifications = collect();

        foreach ($inquiryIDs as $inquiryID) {
            $inquiry = DB::table('inquiry')->where('InquiryID', $inquiryID)->first();

            $progressList = DB::table('inquiryprogress')
                ->where('InquiryID', $inquiryID)
                ->get();

            foreach ($progressList as $progress) {
                // Add Investigation notification if it exists
                if ($progress->InvestigationBeginDate && $progress->VerificationStatus !== 'Rejected') {
                    $notifications->push((object)[
                        'InquiryID' => $inquiry->InquiryID,
                        'InquiryTitle' => $inquiry->InquiryTitle,
                        'Status' => 'Under Investigation',
                        'InvestigationBeginDate' => $progress->InvestigationBeginDate,
                        'VerificationDateTime' => null,
                    ]);
                }

                // Add Verification notification if it exists
                if ($progress->VerificationStatus && $progress->VerificationStatus !== 'Under Investigation') {
                    $notifications->push((object)[
                        'InquiryID' => $inquiry->InquiryID,
                        'InquiryTitle' => $inquiry->InquiryTitle,
                        'Status' => $progress->VerificationStatus,
                        'VerificationDateTime' => $progress->VerificationDateTime,
                        'InvestigationBeginDate' => $progress->InvestigationBeginDate,
                    ]);
                }
            }
        }

        // Sort by latest date
        $notifications = $notifications->sortByDesc(function ($notif) {
            return $notif->VerificationDateTime ?? $notif->InvestigationBeginDate ?? now()->subYears(10); // fallback
        })->values();


        return view('InquiryProgressTrackingUI.Public.NotificationListUI', [
            'notifications' => $notifications
        ]);
    }
    public function p_ProgAllInquiry(Request $request)
    {
        $inquiryID = $request->query('id');

        if (!$inquiryID) {
            abort(404, 'Inquiry ID not provided.');
        }

        $inquiry = DB::table('inquiry')->where('InquiryID', $inquiryID)->first();

        if (!$inquiry) {
            abort(404, 'Inquiry not found.');
        }

        $progressList = DB::table('inquiryprogress')
            ->where('InquiryID', $inquiryID)
            ->get(); // Removed agency join and AgencyName select

        return view('InquiryProgressTrackingUI.Public.ProgAllInquiryUI', [
            'inquiry' => $inquiry,
            'progressList' => $progressList
        ]);
    }

    public function p_ListAllInquiry(Request $request)
    {
        $userID = Auth::user()->UserID;

        $statusFilter = $request->input('status'); // 'Under Investigation', 'Verified as True', 'Identified as Fake'
        $ownOnly = $request->input('own_only'); // 'on' if checked

        $query = DB::table('inquiry')
            ->join('inquiryprogress', 'inquiry.InquiryID', '=', 'inquiryprogress.InquiryID')
            ->select('inquiry.*', 'inquiryprogress.VerificationStatus')
            ->where('inquiryprogress.VerificationStatus', '!=', 'Rejected'); // ⛔️ Exclude Rejected

        if ($statusFilter) {
            $query->where('inquiryprogress.VerificationStatus', $statusFilter);
        }

        if ($ownOnly) {
            $query->join('publicuser', 'inquiry.PublicID', '=', 'publicuser.PublicID')
                ->where('publicuser.UserID', $userID);
        }

        $inquiries = $query->get();

        return view('InquiryProgressTrackingUI.Public.ListAllInquiryUI', [
            'inquiries' => $inquiries,
            'statusFilter' => $statusFilter,
            'ownOnly' => $ownOnly
        ]);
    }
}
