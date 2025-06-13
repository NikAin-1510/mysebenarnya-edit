<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Inquiry;
use App\Models\Agency;
use App\Models\InquiryAssignment;

class InquiryAssignmentController extends Controller
{
    // === PUBLIC ===
    public function publicOwnList()
    {
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

    // === MCMC ===

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

    public function storeAssignment(Request $request)
    {
        $request->validate([
            'inquiry_id' => 'required',
            'agency_id' => 'required',
            'comments' => 'nullable|string'
        ]);

        InquiryAssignment::create([
            'AssignmentID' => uniqid('ASS'),
            'InquiryID' => $request->inquiry_id,
            'AgencyID' => $request->agency_id,
            'mcmcID' => session('profile_id') ?? 'M001',
            'AssignDate' => now(),
            'JurisdictionStatus' => 1,
            'InquiryComment' => $request->comments,
            'AgencyName' => Agency::find($request->agency_id)->AgencyName ?? '',
        ]);

        Inquiry::where('InquiryID', $request->inquiry_id)
            ->update(['SubmissionStatus' => 'assigned']);

        return redirect()->route('agency.assign.form')->with('success', 'Inquiry assigned successfully.');
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
}
