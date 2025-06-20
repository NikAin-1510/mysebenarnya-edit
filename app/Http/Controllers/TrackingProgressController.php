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
            'VerificationStatus' => 'required|in:Under Investigation,Verified as True,Identified as Fake,Rejected',
            'InvestigationDetails' => 'nullable|string',
            'InvestigationDoc' => 'nullable|file|max:2048',
            'Notify' => 'nullable|in:Further clarification needed,Inquiry is completed,Reassignment requested',
        ]);

        $data = [
            'InquiryID' => $request->InquiryID,
            'AgencyID' => $request->AgencyID,
            'InvestigationDetails' => $request->InvestigationDetails,
            'Notify' => $request->Notify,
        ];

        $selectedStatus = $request->VerificationStatus;

        $existing = DB::table('inquiryprogress')
            ->where('InquiryID', $request->InquiryID)
            ->where('AgencyID', $request->AgencyID)
            ->first();

        // Special logic for Under Investigation
        if ($selectedStatus === 'Under Investigation') {
            // Only set InvestigationBeginDate if it doesn't exist OR is null
            if (!$existing || empty($existing->InvestigationBeginDate)) {
                $data['InvestigationBeginDate'] = now();
            }
            // Clear verification fields when going back to investigation
            $data['VerificationStatus'] = null;
            $data['VerificationDateTime'] = null;
        } else {
            // For all other statuses (verified, fake, rejected)
            $data['VerificationStatus'] = $selectedStatus;
            $data['VerificationDateTime'] = now();
            // Don't touch InvestigationBeginDate for verification statuses
        }

        // File upload
        if ($request->hasFile('InvestigationDoc')) {
            $filename = $request->file('InvestigationDoc')->store('investigation_docs', 'public');
            $data['InvestigationDoc'] = $filename;
        }

        // Insert or update
        if ($existing) {
            DB::table('inquiryprogress')
                ->where('InquiryID', $request->InquiryID)
                ->where('AgencyID', $request->AgencyID)
                ->update($data);
        } else {
            $assignment = DB::table('inquiryassignment')
                ->where('InquiryID', $request->InquiryID)
                ->first();

            if ($assignment) {
                $data['AssignmentID'] = $assignment->AssignmentID;
                $data['StatusID'] = 'ST' . strtoupper(Str::random(6));
                DB::table('inquiryprogress')->insert($data);
            } else {
                return back()->with('error', 'Assignment not found for this inquiry and agency.');
            }
        }

        return redirect()->route('progress.update.status')->with('success', 'Inquiry status saved.');
    }


    public function m_SupportingDoc($statusID)
    {
        $record = DB::table('inquiryprogress')
            ->where('StatusID', $statusID)
            ->select('InvestigationDoc')
            ->first();

        if (!$record || !$record->InvestigationDoc) {
            abort(404, 'Document not found.');
        }

        $filePath = storage_path('app/public/' . $record->InvestigationDoc);

        if (!file_exists($filePath)) {
            abort(404, 'File not found on server.');
        }

        return response()->file($filePath);
    }

    //mcmc
    public function m_InquiryProgress(Request $request)
    {
        $inquiryID = $request->query('id');

        if (!$inquiryID) {
            abort(404, 'Inquiry ID not provided.');
        }

        $inquiry = DB::table('inquiry')
            ->where('InquiryID', $inquiryID)
            ->first();

        if (!$inquiry) {
            abort(404, 'Inquiry not found.');
        }

        $progressList = DB::table('inquiryprogress')
            ->join('agency', 'inquiryprogress.AgencyID', '=', 'agency.AgencyID')
            ->where('inquiryprogress.InquiryID', $inquiryID)
            ->select(
                'inquiryprogress.StatusID',
                'inquiryprogress.VerificationStatus',
                'inquiryprogress.VerificationDateTime',
                'inquiryprogress.InvestigationBeginDate',
                'inquiryprogress.InvestigationDetails',
                'inquiryprogress.InvestigationDoc',
                'inquiryprogress.Notify',
                'agency.AgencyName'
            )
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
    public function p_ProgOwnInquiry(Request $request)
    {
        $inquiryID = $request->query('id');

        if (!$inquiryID) {
            abort(404, 'Inquiry ID not provided.');
        }

        $userID = Auth::user()->UserID;

        // Join publicuser to get the inquiry that belongs to this user
        $inquiry = DB::table('inquiry')
            ->join('publicuser', 'inquiry.PublicID', '=', 'publicuser.PublicID')
            ->where('inquiry.InquiryID', $inquiryID)
            ->where('publicuser.UserID', $userID)
            ->select('inquiry.*') // Select inquiry columns only
            ->first();

        if (!$inquiry) {
            abort(403, 'You are not authorized to view this inquiry.');
        }

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

        $statusFilter = $request->input('status'); // 'Under Investigation', etc.
        $ownOnly = $request->input('own_only'); // 'on' if checked

        $query = DB::table('inquiry')
            ->join('inquiryprogress', 'inquiry.InquiryID', '=', 'inquiryprogress.InquiryID')
            ->select('inquiry.*', 'inquiryprogress.VerificationStatus', 'inquiryprogress.InvestigationBeginDate', 'inquiryprogress.VerificationDateTime')
            ->where(function ($q) {
                $q->whereNull('inquiryprogress.VerificationStatus')
                    ->orWhere('inquiryprogress.VerificationStatus', '!=', 'Rejected'); // Exclude rejected
            });

        // ✅ Handle special logic for "Under Investigation"
        if ($statusFilter === 'Under Investigation') {
            $query->whereNotNull('inquiryprogress.InvestigationBeginDate')
                ->whereNull('inquiryprogress.VerificationStatus')
                ->whereNull('inquiryprogress.VerificationDateTime');
        }
        // ✅ Handle other statuses
        elseif (in_array($statusFilter, ['Verified as True', 'Identified as Fake'])) {
            $query->where('inquiryprogress.VerificationStatus', $statusFilter);
        }

        // ✅ Filter by own inquiries
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
