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
use App\Models\InquiryProgress;
use PDF;
use App\Exports\InquiryAssignmentExport;
use Maatwebsite\Excel\Facades\Excel;


class InquiryAssignmentController extends Controller
{
    public function m_ReviewInquiry($id)
    {
        $inquiry = Inquiry::with('latestAssignment.agency')->where('InquiryID', $id)->firstOrFail();

        return view('InquiryAssignmentUI.MCMC.ReviewInquiryUI', compact('inquiry'));
    }


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
            ->get()
            ->unique('InquiryID');

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
        $validated = $request->validate([
            'AgencyID' => 'required',
            'InquiryComment' => 'required|string|max:255',
        ]);

        $assignmentID = 'ASS' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        $assignment = new InquiryAssignment();
        $assignment->AssignmentID = $assignmentID;
        $assignment->AgencyID = $validated['AgencyID'];
        $assignment->mcmcID = Auth::user()->UserID;
        $assignment->InquiryID = $id;
        $assignment->AssignDate = now()->toDateString();
        $assignment->JurisdictionStatus = 1;
        $assignment->InquiryComment = $validated['InquiryComment'];
        $assignment->save();

        // ✅ Update inquiry submission status
        $inquiry = Inquiry::findOrFail($id);
        $inquiry->SubmissionStatus = 'assigned'; // <-- Add this
        $inquiry->save(); // <-- Don't forget to save it

        return redirect()->route('mcmc.new.inquiry')->with('success', 'Inquiry assigned successfully.');
    }



    public function a_AssignInquiry()
    {
        $inquiries = Inquiry::where('SubmissionStatus', 'pending')->get();
        $agencies = Agency::all();
        $assignments = InquiryAssignment::with(['inquiry', 'agency'])->latest('AssignDate')->get();

        return view('InquiryAssignmentUI.AssignInquiryUI', compact('inquiries', 'agencies', 'assignments'));
    }

    public function displayReportDashboard(Request $request)
    {
        $agencies = Agency::all();

        $query = InquiryAssignment::with('agency');

        if ($request->start_date) {
            $query->where('AssignDate', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('AssignDate', '<=', $request->end_date);
        }
        if ($request->agency_id) {
            $query->where('AgencyID', $request->agency_id);
        }

        $assignments = $query->get()->groupBy('agency.AgencyName');
        $agencyNames = $assignments->keys();
        $agencyCounts = $assignments->map->count()->values();

        return view('InquiryAssignmentUI.MCMC.DisplayReportUI', compact(
            'agencies',
            'agencyNames',
            'agencyCounts'
        ));
    }

    public function a_ListAssignedInquiry()
    {
        $user = Auth::user();

        // Get agency based on logged-in UserID
        $agency = \App\Models\Agency::where('UserID', $user->UserID)->first();

        if (!$agency) {
            return redirect()->route('display.home')->with('error', 'You are not authorized as agency user.');
        }

        // Get all assigned inquiries for this agency
        $assignments = InquiryAssignment::with(['inquiry', 'progress', 'agency'])
            ->where('AgencyID', $agency->AgencyID)
            ->where('JurisdictionStatus', '!=', 0)
            ->whereIn('AssignmentID', function ($query) use ($agency) {
                $query->select(DB::raw('MAX(AssignmentID)'))
                    ->from('inquiryassignment')
                    ->where('AgencyID', $agency->AgencyID)
                    ->groupBy('InquiryID');
            })
            ->orderBy('AssignDate', 'desc')
            ->get();


        // Pass the data to blade
        return view('InquiryAssignmentUI.Agency.ListAssignedInquiryUI', compact('assignments'));
    }

    public function a_InquiryDetails($id)
    {
        $assignment = InquiryAssignment::with(['inquiry', 'progress'])
            ->where('AssignmentID', $id)
            ->firstOrFail();

        return view('InquiryAssignmentUI.Agency.JurisdictionReviewUI', compact('assignment'));
    }

    public function handleAction(Request $request, $id)
    {
        $assignment = InquiryAssignment::findOrFail($id);
        $inquiry = $assignment->inquiry;

        if ($request->action === 'accept') {
            // Update the assignment with jurisdiction status and comment
            $assignment->update([
                'JurisdictionStatus' => 1,
                'JurisdictionComment' => 'Accepted and proceeding with investigation'
            ]);

            InquiryProgress::updateOrCreate(
                ['AssignmentID' => $assignment->AssignmentID],
                [
                    'InquiryID' => $assignment->InquiryID,
                    'AgencyID' => $assignment->AgencyID,
                    'InvestigationBeginDate' => now(),
                    'VerificationStatus' => 'ACCEPTED',
                    'InvestigationDetails' => 'Inquiry accepted by agency.',
                ]
            );
            return redirect()->route('agency.inquirylist')->with('success', 'Inquiry accepted.');
        }

        if ($request->action === 'reject') {
            // Validate the jurisdiction comment
            $request->validate([
                'jurisdictionComment' => 'required|string|max:100'
            ]);

            DB::beginTransaction();
            try {
                // Update the assignment with jurisdiction status and comment
                $assignment->update([
                    'JurisdictionStatus' => 0,
                    'JurisdictionComment' => $request->jurisdictionComment
                ]);

                // Create inquiry progress record
                InquiryProgress::create([
                    'StatusID' => 'ST' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                    'AssignmentID' => $assignment->AssignmentID,
                    'InquiryID' => $assignment->InquiryID,
                    'AgencyID' => $assignment->AgencyID,
                    'InvestigationBeginDate' => now(),
                    'VerificationStatus' => 'REJECTED',
                    'InvestigationDetails' => $request->jurisdictionComment,
                ]);

                // Reset inquiry status to pending so it can be reassigned
                $inquiry->SubmissionStatus = 'pending';
                $inquiry->SubmissionCategory = null;
                $inquiry->save();

                DB::commit();
                return redirect()->route('agency.inquirylist')->with('success', 'Inquiry rejected and returned for reassignment.');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Failed to reject: ' . $e->getMessage());
            }
        }

        return back()->with('error', 'Invalid action.');
    }

    // Inquiry Report Page
    public function showInquiryReport(Request $request)
    {
        $agencies = Agency::all();

        $query = InquiryAssignment::with('agency');

        if ($request->start_date) {
            $query->where('AssignDate', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('AssignDate', '<=', $request->end_date);
        }
        if ($request->agency_id) {
            $query->where('AgencyID', $request->agency_id);
        }

        $assignments = $query->get();

        $grouped = $assignments->groupBy(function ($item) {
            return $item->agency->AgencyName;
        });

        $agencyNames = $grouped->keys()->toArray();
        $agencyCounts = $grouped->map(function ($items) {
            return $items->count();
        })->values()->toArray();

        return view('InquiryAssignmentUI.MCMC.DisplayReportUI', compact('agencies', 'agencyNames', 'agencyCounts'));
    }

    // Export PDF
    public function exportInquiryReportExcel(Request $request)
    {
        $query = InquiryAssignment::with('agency');

        if ($request->start_date) {
            $query->where('AssignDate', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('AssignDate', '<=', $request->end_date);
        }
        if ($request->agency_id) {
            $query->where('AgencyID', $request->agency_id);
        }

        $assignments = $query->get();

        $grouped = $assignments->groupBy(function ($item) {
            return $item->agency->AgencyName;
        });

        $report = [];

        foreach ($grouped as $agencyName => $items) {
            $assigned = $items->count();

            $report[] = [
                'agency' => $agencyName,
                'assigned' => $assigned
            ];
        }

        $overallStats = [
            'totalAssigned' => $assignments->count(),
        ];

        return Excel::download(new \App\Exports\InquiryAssignmentExport([
            'report' => $report,
            'overallStats' => $overallStats
        ]), 'Inquiry_Assignment_Report.xlsx');
    }
}
