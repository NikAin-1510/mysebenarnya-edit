<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InquiryAssignmentController extends Controller
{
    public function publicOwnList()
    {
        // Fallback PublicID for testing (bypass login if not set)
        $publicID = session('profile_id') ?? 'PU001';

        $inquiries = DB::table('inquiry')
            ->leftJoin('inquiryasssignment', 'inquiry.InquiryID', '=', 'inquiryasssignment.InquiryID')
            ->leftJoin('agency', 'inquiryasssignment.AgencyID', '=', 'agency.AgencyID')
            ->leftJoin('inquiryprogress', 'inquiry.InquiryID', '=', 'inquiryprogress.InquiryID')
            ->select(
                'inquiry.*',
                'agency.AgencyName',
                'inquiryasssignment.AssignDate',
                'inquiryprogress.VerificationDateTime',
                'inquiryprogress.VerificationStatus',
                'inquiryprogress.InvestigationDetails'
            )
            ->where('inquiry.PublicID', $publicID)
            ->orderBy('SubmissionDate', 'desc')
            ->get();

        return view('InquiryAssignmentUI.Public.OwnListInquiryUI', compact('inquiries'));
    }
}
