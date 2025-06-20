<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Inquiry;
use App\Models\Agency;
use App\Models\InquiryAssignment;
use Illuminate\Support\Facades\Auth;
use App\Models\PublicUser;
use App\Models\User;

class InquiryAssignmentController extends Controller
{
    // === PUBLIC ===
    public function publicOwnList()
    {
        $user = Auth::user();

        if (!$user || !$user->publicUser) {
            return redirect()->route('login')->with('error', 'You must be logged in as a public user.');
        }

        $publicID = $user->publicUser->PublicID;

        $inquiries = DB::table('inquiry')
            ->leftJoin('inquiryassignment', 'inquiry.InquiryID', '=', 'inquiryassignment.InquiryID')
            ->leftJoin('agency', 'inquiryassignment.AgencyID', '=', 'agency.AgencyID')
            ->leftJoin('inquiryprogress', 'inquiry.InquiryID', '=', 'inquiryprogress.InquiryID')
            ->select(
                'inquiry.*',
                'agency.AgencyName',
                'inquiryassignment.AssignDate',
                'inquiryprogress.VerificationDateTime',
                'inquiryprogress.VerificationStatus',
                'inquiryprogress.InvestigationDetails'
            )
            ->where('inquiry.PublicID', $publicID) // now using auth()->user()
            ->orderBy('SubmissionDate', 'desc')
            ->get();

        return view('InquiryAssignmentUI.Public.OwnAssignedInquiryUI', compact('inquiries'));
    }


    // === MCMC ===

// In your Controller (e.g., McmcController or InquiryController)

public function assignInquiry(Request $request, $inquiryId)
{
    try {
        // Validate the request
        $request->validate([
            'AgencyID' => 'required|string',
            'InquiryComment' => 'required|string|max:30',
        ]);

        // Get the inquiry
        $inquiry = DB::table('inquiry')->where('InquiryID', $inquiryId)->first();
        
        if (!$inquiry) {
            return redirect()->back()->with('error', 'Inquiry not found.');
        }

        // Get MCMC user (assuming you have auth or session)
        $mcmcUser = DB::table('mcmc')
            ->join('user', 'mcmc.UserID', '=', 'user.UserID')
            ->where('user.UserID', auth()->user()->UserID ?? 'P003') // Replace with actual auth
            ->first();

        // Generate Assignment ID
        $lastAssignment = DB::table('inquiryassignment')
            ->orderBy('AssignmentID', 'desc')
            ->first();
        
        $nextNumber = 1;
        if ($lastAssignment) {
            $nextNumber = intval(substr($lastAssignment->AssignmentID, 3)) + 1;
        }
        $assignmentId = 'ASS' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // Create the assignment
        DB::table('inquiryassignment')->insert([
            'AssignmentID' => $assignmentId,
            'AgencyID' => $request->AgencyID,
            'mcmcID' => $mcmcUser->mcmcID,
            'InquiryID' => $inquiryId,
            'AssignDate' => now()->toDateString(),
            'JurisdictionStatus' => 1,
            'InquiryComment' => $request->InquiryComment,
        ]);

        // Update inquiry status to 'forwarded' or 'assigned'
        DB::table('inquiry')
            ->where('InquiryID', $inquiryId)
            ->update(['SubmissionStatus' => 'forwarded']);

        // Redirect with success message
        return redirect()->route('mcmc.new.inquiry')
            ->with('success', 'Inquiry has been successfully assigned to the agency.');

    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Failed to assign inquiry. Please try again.')
            ->withInput();
    }
}

public function newInquiryList()
{
    // Get only pending inquiries (not yet assigned)
    $inquiries = DB::table('inquiry')
        ->join('publicuser', 'inquiry.PublicID', '=', 'publicuser.PublicID')
        ->leftJoin('inquiryassignment', 'inquiry.InquiryID', '=', 'inquiryassignment.InquiryID')
        ->whereNull('inquiryassignment.InquiryID') // Only unassigned inquiries
        ->where('inquiry.SubmissionStatus', '!=', 'forwarded') // Exclude already forwarded
        ->where('inquiry.SubmissionStatus', '!=', 'completed') // Exclude completed
        ->select('inquiry.*')
        ->orderBy('inquiry.SubmissionDate', 'desc')
        ->get();

    return view('mcmc.new-inquiry', compact('inquiries'));
}
}