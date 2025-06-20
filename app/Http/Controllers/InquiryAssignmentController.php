<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Inquiry;
use App\Models\Agency;
use App\Models\InquiryAssignment;
use Illuminate\Support\Facades\Auth;
use App\Models\PublicUser;

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

    public function showAssignForm($id)
{
    $inquiry = \App\Models\Inquiry::findOrFail($id);
    $agencies = \App\Models\Agency::select('AgencyID', 'AgencyName')->distinct()->get();

    return view('InquiryAssignment.MCMC.AssignInquiryUI', compact('inquiry', 'agencies'));
}

public function storeAssignment(Request $request, $id)
{
    $validated = $request->validate([
        'AgencyID' => 'required',
        'InquiryComment' => 'required|string|max:255'
    ]);

    $assignment = new \App\Models\InquiryAssignment();
    $assignment->AssignmentID = 'ASS' . str_pad(rand(1, 9999), 3, '0', STR_PAD_LEFT);
    $assignment->AgencyID = $validated['AgencyID'];
    $assignment->mcmcID = auth()->user()->UserID; // assuming MCMC user is logged in
    $assignment->InquiryID = $id;
    $assignment->AssignDate = now()->toDateString();
    $assignment->JurisdictionStatus = 1;
    $assignment->InquiryComment = $validated['InquiryComment'];
    $assignment->save();

    return redirect()->route('mcmc.new.inquiry')->with('success', 'Inquiry successfully assigned!');
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

        return view('InquiryAssignmentUI.MCMC.AssignInquiryUI', compact('inquiries', 'agencies', 'assignments'));
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

    public function handleAction(Request $request, $id)
    {
        $assignment = InquiryAssignment::findOrFail($id);
        $inquiry = $assignment->inquiry;

        if ($request->action === 'accept') {
            InquiryProgress::updateOrCreate(
                ['AssignmentID' => $assignment->AssignmentID],
                [
                    'InquiryID' => $assignment->InquiryID,
                    'AgencyID' => $assignment->AgencyID,
                    'InvestigationBeginDate' => now(),
                    'VerificationStatus' => 'accepted',
                    'InvestigationDetails' => 'Inquiry accepted by agency.',
                ] //
            );
            return redirect()->route('agency.inquiries')->with('success', 'Inquiry accepted.');
        }

        if ($request->action === 'reject') {
            DB::beginTransaction();
            try {
                // Reset assignment
                $assignment->delete();

                // Update inquiry status back to pending
                $inquiry->SubmissionStatus = 'pending';
                $inquiry->save();

                DB::commit();
                return redirect()->route('agency.inquiries')->with('success', 'Inquiry rejected and unassigned.');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Failed to reject inquiry: ' . $e->getMessage());
            }
        }

        return back()->with('error', 'Invalid action.');
    }
}
