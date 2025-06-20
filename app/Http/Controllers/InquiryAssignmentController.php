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

        // Subquery to get the latest assignment per inquiry
        $latestAssignments = DB::table('inquiryassignment as ia1')
            ->select('ia1.InquiryID', 'ia1.AgencyID', 'ia1.AssignDate')
            ->whereRaw('ia1.AssignDate = (
            SELECT MAX(ia2.AssignDate)
            FROM inquiryassignment as ia2
            WHERE ia2.InquiryID = ia1.InquiryID
        )');

        // Main query to get inquiries with latest assignment, agency info, and progress
        $inquiries = DB::table('inquiry')
            ->leftJoinSub($latestAssignments, 'latest_ia', function ($join) {
                $join->on('inquiry.InquiryID', '=', 'latest_ia.InquiryID');
            })
            ->leftJoin('agency', 'latest_ia.AgencyID', '=', 'agency.AgencyID')
            ->leftJoin('inquiryprogress', 'inquiry.InquiryID', '=', 'inquiryprogress.InquiryID')
            ->select(
                'inquiry.*',
                'agency.AgencyName',
                'latest_ia.AssignDate',
                'inquiryprogress.VerificationDateTime',
                'inquiryprogress.VerificationStatus',
                'inquiryprogress.InvestigationDetails'
            )
            ->where('inquiry.PublicID', $publicID)
            ->orderBy('SubmissionDate', 'desc')
            ->get();

        return view('InquiryAssignmentUI.Public.OwnAssignedInquiryUI', compact('inquiries'));
    }



    // === MCMC ===

    // Show Assign Inquiry Form
    public function showAssignForm($id)
    {
        // Get inquiry data
        $inquiry = Inquiry::findOrFail($id);

        // Get all distinct agencies from database
        $agencies = Agency::select('AgencyID', 'AgencyName')->distinct()->get();

        // Pass to view
        return view('InquiryAssignmentUI.MCMC.AssignInquiryUI', compact('inquiry', 'agencies'));
    }

    // Store Inquiry Assignment
    public function storeAssignment(Request $request, $id)
    {
        // Validate form input
        $validated = $request->validate([
            'AgencyID' => 'required',
            'InquiryComment' => 'required|string|max:255',
        ]);

        // Generate unique AssignmentID (example: ASS1234)
        $assignmentID = 'ASS' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Create new assignment
        $assignment = new InquiryAssignment();
        $assignment->AssignmentID = $assignmentID;
        $assignment->AgencyID = $validated['AgencyID'];
        $assignment->mcmcID = Auth::user()->UserID;
        $assignment->InquiryID = $id;
        $assignment->AssignDate = now()->toDateString();
        $assignment->JurisdictionStatus = 1; // default status
        $assignment->InquiryComment = $validated['InquiryComment'];
        $assignment->save();

        return redirect()->route('mcmc.new.inquiry')->with('success', 'Inquiry assigned successfully.');
    }


    public function m_ReviewInquiry($id)
    {
        $inquiry = Inquiry::with('latestAssignment.agency')->where('InquiryID', $id)->firstOrFail();

        return view('InquiryAssignmentUI.MCMC.ReviewInquiryUI', compact('inquiry'));
    }



    public function a_ReviewInquiry()
    {
        $assignments = InquiryAssignment::with(['inquiry', 'agency', 'progress'])->get();

        $pendingInquiries = Inquiry::where('SubmissionStatus', 'pending')->count();
        return view('InquiryAssignmentUI.MCMC.ReviewInquiryUI', compact('assignments', 'pendingInquiries'));
    }

    public function a_AssignInquiry()
    {
        $inquiries = Inquiry::where('SubmissionStatus', 'pending')->get();
        $agencies = Agency::all();
        $assignments = InquiryAssignment::with(['inquiry', 'agency'])->latest('AssignDate')->get();

        return view('InquiryAssignmentUI.AssignInquiryUI', compact('inquiries', 'agencies', 'assignments'));
    }

    public function a_DisplayReport()
    {
        $agencies = Agency::all();

        $assignments = InquiryAssignment::with('agency')->get()->groupBy('agency.AgencyName');
        $agencyNames = $assignments->keys();
        $agencyCounts = $assignments->map->count()->values();

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        $trendsData = [];

        foreach ($agencies as $agency) {
            $monthlyCounts = [];
            foreach (range(1, 6) as $m) {
                $monthlyCounts[] = InquiryAssignment::where('AgencyID', $agency->AgencyID)
                    ->whereMonth('AssignDate', $m)
                    ->count();
            }

            $trendsData[] = [
                'label' => $agency->AgencyName,
                'data' => $monthlyCounts,
                'borderColor' => '#' . substr(md5($agency->AgencyName), 0, 6),
                'backgroundColor' => 'rgba(0,0,0,0.1)',
                'tension' => 0.4,
            ];
        }

        return view('InquiryAssignmentUI.MCMC.DisplayReportUI', compact(
            'agencies',
            'agencyNames',
            'agencyCounts',
            'months',
            'trendsData'
        ));
    }
    public function a_ListAssignedInquiry()
    {
        $agencyID = session('profile_id') ?? 'A001'; // fallback for testing

        $assignments = InquiryAssignment::with(['inquiry', 'progress'])
            ->where('AgencyID', $agencyID)
            ->get();

        return view('InquiryAssignmentUI.Agency.ListAssignedInquiryUI', compact('assignments'));
    }

    public function a_InquiryDetails($id)
    {
        $assignment = InquiryAssignment::with(['inquiry', 'progress'])
            ->where('AssignmentID', $id)
            ->firstOrFail();

        return view('InquiryFormSubmissionUI.Agency.InquiryDetailsUI', compact('assignment'));
    }
}
