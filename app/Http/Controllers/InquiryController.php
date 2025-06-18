<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response; // ✅ Add this line
use App\Models\Inquiry;
use App\Models\Agency;
use PDF;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Carbon\Carbon;


class InquiryController extends Controller
{
    // === AGENCY ===

    public function a_ListAssignedInquiry(Request $request)
    {
        $agencyId = Auth::user()->AgencyID;

        $query = Inquiry::where('AgencyID', $agencyId);

        if ($request->status) {
            $query->where('InvestigationStatus', $request->status);
        }

        if ($request->category) {
            $query->where('SubmissionCategory', $request->category);
        }

        if ($request->date) {
            $query->whereMonth('SubmissionDate', '=', date('m', strtotime($request->date)))
                ->whereYear('SubmissionDate', '=', date('Y', strtotime($request->date)));
        }

        // ✅ New: Filter by Inquiry Title (partial match)
        if ($request->title) {
            $query->where('InquiryTitle', 'like', '%' . $request->title . '%');
        }

        $assignedInquiries = $query->orderBy('SubmissionDate', 'desc')->get();

        return view('InquiryFormSubmissionUI.Agency.ListAssignedInquiryUI', compact('assignedInquiries'));
    }

    public function a_ReviewInquiry($id)
    {

        return view('InquiryFormSubmissionUI.Agency.ReviewInquiryUI', compact('inquiry', 'historyLogs'));
    }






    // === MCMC ===



    //DISPLAY REPORT

    public function m_DisplayReport(Request $request)
    {
        // Filter logic (optional based on month/year/agency)
        $inquiries = DB::table('inquiry')
            ->leftJoin('inquiryassignment', 'inquiry.InquiryID', '=', 'inquiryassignment.InquiryID')
            ->leftJoin('agency', 'inquiryassignment.AgencyID', '=', 'agency.AgencyID')
            ->select('inquiry.*', 'agency.AgencyName');

        // Apply filters
        if ($request->month) {
            $inquiries->whereMonth('SubmissionDate', $request->month);
        }

        if ($request->year) {
            $inquiries->whereYear('SubmissionDate', $request->year);
        }

        if ($request->agency) {
            $inquiries->where('agency.AgencyID', $request->agency);
        }

        $inquiries = $inquiries->get();

        // Build monthly chart data
        $monthlyCounts = DB::table('inquiry')
            ->select(
                DB::raw('MONTH(SubmissionDate) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy(DB::raw('MONTH(SubmissionDate)'))
            ->orderBy('month')
            ->pluck('total', 'month');

        $chartData = [
            'labels' => [],
            'data' => []
        ];

        foreach (range(1, 12) as $m) {
            $chartData['labels'][] = date('F', mktime(0, 0, 0, $m, 1));
            $chartData['data'][] = $monthlyCounts[$m] ?? 0;
        }

        $agencies = DB::table('agency')->get();

        return view('InquiryFormSubmissionUI.MCMC.DisplayReportUI', compact('inquiries', 'agencies', 'chartData'));
    }

    private function filterReportData(Request $request)
    {
        $query = DB::table('inquiry')
            ->leftJoin('inquiryassignment', 'inquiry.InquiryID', '=', 'inquiryassignment.InquiryID')
            ->leftJoin('agency', 'inquiryassignment.AgencyID', '=', 'agency.AgencyID')
            ->select('inquiry.*', 'agency.AgencyName');

        if ($request->month) {
            $query->whereMonth('SubmissionDate', $request->month);
        }

        if ($request->year) {
            $query->whereYear('SubmissionDate', $request->year);
        }

        if ($request->agency) {
            $query->where('agency.AgencyID', $request->agency);
        }

        return $query->get();
    }

    public function exportReportToPDF(Request $request) {}


    public function exportReportToExcel(Request $request) {}



    // === PUBLIC ===



    public function p_DetailsOwnInquiry()
    {
        // Get the logged-in user's associated PublicID using the relationship
        $publicID = auth()->user()->publicProfile->PublicID ?? null;


        if (!$publicID) {
            return redirect()->back()->with('error', 'Public user profile not found.');
        }

        // Fetch inquiries using PublicID
        $inquiries = Inquiry::where('PublicID', $publicID)
            ->orderBy('SubmissionDate', 'desc')
            ->get();

        return view('InquiryFormSubmissionUI.Public.DetailsOwnInquiryUI', compact('inquiries'));
    }





    // INQUIRY FORM //
    public function p_InquiryForm(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'url' => 'required|url',
            'evidence' => 'nullable|file|max:5120', // 5MB
        ]);

        $inquiry = new Inquiry();
        $inquiry->InquiryID = uniqid('INQ');
        $inquiry->UserID = Auth::id(); // Assuming user is logged in
        $inquiry->Title = $request->input('title');
        $inquiry->Description = $request->input('description');
        $inquiry->URL = $request->input('url');
        $inquiry->Status = 'Submitted';
        $inquiry->DateSubmitted = now();

        // Handle file upload
        if ($request->hasFile('evidence')) {
            $file = $request->file('evidence');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('evidence', $filename, 'public'); // save in storage/app/public/evidence
            $inquiry->EvidencePath = $path;
        }

        $inquiry->save();

        return redirect()->back()->with('success', 'Form successfully submitted!');
    }

    public function p_DetailsAllInquiry()
    {
        return view('InquiryFormSubmissionUI.Public.DetailsAllInquiryUI');
    }

    public function create()
    {
        return view('InquiryFormSubmissionUI.Public.InquiryFormUI');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:100',
            'details' => 'required|string|max:1000',
            'type_link' => 'required|url|max:255',
            'evidence' => 'nullable|file|max:5120', // 5MB
        ]);

        $inquiryID = 'IQ' . str_pad(DB::table('inquiry')->count() + 1, 6, '0', STR_PAD_LEFT);

        $filePath = null;
        if ($request->hasFile('evidence')) {
            $filePath = $request->file('evidence')->store('evidence', 'public');
        }

        DB::table('inquiry')->insert([
            'InquiryID' => $inquiryID,
            'PublicID' => Auth::user()->publicUser->PublicID,
            'InquiryTitle' => $validated['subject'],
            'InquiryDescription' => $validated['details'],
            'SubmissionDate' => now(),
            'SubmissionStatus' => 'Pending',
            'SubmissionLink' => $validated['type_link'],
            'SubmissionEvidence' => $filePath ? basename($filePath) : null,
        ]);

        return redirect()->route('inquiry.form')->with('success', 'Form successfully submitted!');
    }





    // LIST INQUIRY
    public function m_ListInquiry()
    {
        $inquiries = DB::table('inquiry')
            ->join('publicuser', 'inquiry.publicID', '=', 'publicuser.publicID')
            ->join('user', 'publicuser.userID', '=', 'user.UserID')
            ->where('user.Role', 'publicuser')
            ->orderBy('inquiry.SubmissionDate', 'desc')
            ->select('inquiry.*')
            ->get();

        return view('InquiryFormSubmissionUI.MCMC.ListInquiryUI', compact('inquiries'));
    }
    //----

    // DETAILS INQUIRY
    public function UpdateCategory(Request $request, $id)
    {
        $request->validate([
            'SubmissionCategory' => 'required|in:Genuine,NonSerious',
        ]);

        $inquiry = Inquiry::where('PublicID', $id)->firstOrFail();
        $inquiry->SubmissionCategory = $request->SubmissionCategory;
        $inquiry->save();

        return back()->with('success', 'Category updated.');
    }

    public function m_DetailsInquiry($id)
    {
        $inquiry = Inquiry::where('PublicID', $id)->firstOrFail();
        return view('InquiryFormSubmissionUI.MCMC.DetailsInquiryUI', compact('inquiry'));
    }

    //-----

    // LIST ALL INQUIRY

    public function m_ListAllInquiry(Request $request)
    {
        $query = Inquiry::query();

        if ($request->filled('status')) {
            $query->where('SubmissionCategory', $request->status);
        }

        if ($request->filled('agency')) {
            $query->where('AssignedAgencyID', $request->agency);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('SubmissionDate', [$request->start_date, $request->end_date]);
        }

        $inquiries = $query->orderByDesc('SubmissionDate')->get();
        $agencies = Agency::all();

        return view('InquiryFormSubmissionUI.MCMC.ListAllInquiryUI', compact('inquiries', 'agencies'));
    }
}
