<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response; // ✅ Add this line
use App\Models\Inquiry;
use App\Models\Agency;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\InquiryAssignment;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\InquiryReport;
use App\Exports\InquiryReportExport;


class InquiryController extends Controller
{
    // === AGENCY ===
    public function a_ListAssignedInquiry(Request $request)
    {
        $agencyID = \App\Models\Agency::where(
            'UserID',
            Auth::user()->UserID
        )->value('AgencyID');

        $query = \App\Models\Inquiry::whereHas('latestAssignment', function ($q) use ($agencyID) {
            $q->where('AgencyID', $agencyID);
        })
            ->whereHas('progress', function ($q) use ($agencyID) {
                $q->where('AgencyID', $agencyID)
                    ->whereIn('VerificationStatus', ['Verified as True', 'Identified as Fake']);
            })
            ->with(['latestAssignment', 'latestProgress'])
            ->orderByDesc('SubmissionDate');

        // Filters
        if ($request->filled('status')) {
            $statusMap = [
                'verified' => 'Verified as True',
                'fake' => 'Identified as Fake',
            ];

            if (isset($statusMap[$request->status])) {
                $query->whereHas('progress', function ($q) use ($agencyID, $statusMap, $request) {
                    $q->where('AgencyID', $agencyID)
                        ->where('VerificationStatus', $statusMap[$request->status]);
                });
            }
        }

        if ($request->filled('category')) {
            $query->where('SubmissionCategory', $request->category);
        }

        if ($request->filled('date')) {
            $query->whereMonth('SubmissionDate', date('m', strtotime($request->date)))
                ->whereYear('SubmissionDate', date('Y', strtotime($request->date)));
        }

        if ($request->filled('title')) {
            $query->where('InquiryTitle', 'like', '%' . $request->title . '%');
        }

        $assignedInquiries = $query->get();

        return view('InquiryFormSubmissionUI.Agency.ListAssignedInquiry', compact('assignedInquiries'));
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
            ->leftJoin('inquiryassignment', 'inquiry.InquiryID', '=', 'inquiryassignment.InquiryID')
            ->leftJoin('agency', 'inquiryassignment.AgencyID', '=', 'agency.AgencyID')
            ->select(DB::raw('MONTH(SubmissionDate) as month'), DB::raw('COUNT(*) as total'))
            ->when($request->year, function ($query) use ($request) {
                $query->whereYear('SubmissionDate', $request->year);
            })
            ->when($request->month, function ($query) use ($request) {
                $query->whereMonth('SubmissionDate', $request->month);
            })
            ->when($request->agency, function ($query) use ($request) {
                $query->where('agency.AgencyID', $request->agency);
            })
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

        return view('InquiryFormSubmissionUI.MCMC.DisplayReportInquiryUI', compact('inquiries', 'agencies', 'chartData'));
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



    public function exportReportToPDF(Request $request)
    {
        $agencies = DB::table('agency')->get();

        // Apply filters like the dashboard
        $inquiries = DB::table('inquiry')
            ->leftJoin('inquiryassignment', 'inquiry.InquiryID', '=', 'inquiryassignment.InquiryID')
            ->leftJoin('agency', 'inquiryassignment.AgencyID', '=', 'agency.AgencyID')
            ->select('inquiry.*', 'agency.AgencyName');

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

        // Load PDF view
        $pdf = Pdf::loadView('InquiryFormSubmissionUI.MCMC.ReportPDFUI', compact('inquiries', 'agencies'));

        return $pdf->download('Inquiry_Report.pdf');
    }


    public function exportReportToExcel(Request $request)
    {
        $query = \App\Models\Inquiry::query()
            ->leftJoin('inquiryassignment', 'inquiry.InquiryID', '=', 'inquiryassignment.InquiryID')
            ->leftJoin('agency', 'inquiryassignment.AgencyID', '=', 'agency.AgencyID')
            ->select('inquiry.*', 'agency.AgencyName');

        if ($request->filled('month')) {
            $query->whereMonth('inquiry.SubmissionDate', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('inquiry.SubmissionDate', $request->year);
        }

        if ($request->filled('agency')) {
            $query->where('inquiryassignment.AgencyID', $request->agency);
        }

        $inquiries = $query->get();

        return Excel::download(new InquiryReport($inquiries), 'inquiry_report.xlsx');
    }



    // === PUBLIC ===





    // INQUIRY FORM //
    public function p_InquiryForm(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'url' => 'required|url',
            'evidence' => 'nullable|file|max:5120', // 5MB
        ]);
        // Example PHP logic to generate next InquiryID


        $inquiry = new Inquiry();
        $inquiry->InquiryID = uniqid('IQQ');
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

    public function p_DetailsAllInquiry($id)
    {
        $inquiry = Inquiry::findOrFail($id);

        // Set status based on current inquiry state
        if (!empty($inquiry->VerificationStatus)) {
            $inquiry->SubmissionStatus = 'Completed';
        } elseif (!empty($inquiry->InvestigationBeginDate) || $inquiry->latestAssignment) {
            $inquiry->SubmissionStatus = 'Forwarded';
        } else {
            $inquiry->SubmissionStatus = 'Pending';
        }

        $inquiry->save();

        $assignedAgency = $inquiry->latestAssignment->agency ?? null;


        $nextInquiry = Inquiry::where('SubmissionDate', '>', $inquiry->SubmissionDate)
            ->orderBy('SubmissionDate')
            ->first();

        return view('InquiryFormSubmissionUI.Public.DetailsAllInquiryUI', compact('inquiry', 'assignedAgency', 'nextInquiry'));
    }



    public function p_DetailsOwnInquiry($id)
    {
        $user = Auth::user();

        if (!$user || !$user->publicUser) {
            return redirect()->back()->with('error', 'Your public user profile is missing.');
        }

        $inquiry = Inquiry::where('InquiryID', $id)
            ->where('PublicID', $user->publicUser->PublicID)
            ->with(['progress']) // eager load progress table
            ->firstOrFail();

        $assignedAgency = InquiryAssignment::with(['agency', 'mcmc'])
            ->where('InquiryID', $id)
            ->whereNotNull('AgencyID')
            ->orderByDesc('AssignDate')
            ->first();

        // ✅ Auto-determine SubmissionStatus
        if (!empty($inquiry->progress?->VerificationStatus)) {
            $inquiry->SubmissionStatus = 'Completed';
        } elseif (!empty($inquiry->progress?->InvestigationBeginDate)) {
            $inquiry->SubmissionStatus = 'Forwarded';
        } elseif ($assignedAgency) {
            $inquiry->SubmissionStatus = 'Forwarded';
        } else {
            $inquiry->SubmissionStatus = 'Pending';
        }

        return view('InquiryFormSubmissionUI.Public.DetailsOwnInquiryUI', compact(
            'inquiry',
            'assignedAgency'
        ));
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

        if (!Auth::check() || !Auth::user()->publicUser) {
            return redirect()->route('login')->with('error', 'Please login as a public user before submitting an inquiry.');
        }

        $publicID = Auth::user()->publicUser->PublicID;
        $inquiryID = 'IQ' . str_pad(DB::table('inquiry')->count() + 1, 5, '0', STR_PAD_LEFT);

        $filePath = null;
        if ($request->hasFile('evidence')) {
            $filePath = $request->file('evidence')->store('evidence', 'public');
        }

        DB::table('inquiry')->insert([
            'InquiryID' => $inquiryID,
            'PublicID' => $publicID,
            'InquiryTitle' => $validated['subject'],
            'InquiryDescription' => $validated['details'],
            'SubmissionDate' => now(),
            'SubmissionStatus' => 'pending',
            'SubmissionLink' => $validated['type_link'],
            'SubmissionEvidence' => $filePath ? basename($filePath) : null,
        ]);

        return redirect()->route('inquiry.form')->with('success', 'Form successfully submitted!');
    }





    // LIST INQUIRY
    public function m_ListInquiry()
    {
        $inquiries = Inquiry::with('latestAssignment.agency')
            ->whereRaw('LOWER(SubmissionStatus) = ?', ['pending'])
            ->get();

        return view('InquiryFormSubmissionUI.MCMC.ListInquiryUI', compact('inquiries'));
    }



    //----

    // DETAILS INQUIRY
    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'SubmissionCategory' => 'required|in:Serious,Non-Serious',
        ]);

        $inquiry = Inquiry::findOrFail($id);
        $inquiry->SubmissionCategory = $request->SubmissionCategory;

        // Move the inquiry out of the New Inquiry queue after saving
        $inquiry->SubmissionStatus = 'Forwarded';
        $inquiry->save();

        return redirect()->route('mcmc.all.inquiry')->with('success', 'Inquiry saved and moved to List Inquiry.');
    }


    public function m_DetailsInquiry($id)
    {
        $inquiry = Inquiry::where('InquiryID', $id)->firstOrFail();
        return view('InquiryFormSubmissionUI.MCMC.DetailsInquiryUI', compact('inquiry'));
    }



    //-----

    // LIST ALL INQUIRY
    public function m_ListAllInquiry(Request $request)
    {
        $query = Inquiry::with(['latestAssignment.agency']);


        if ($request->filled('status')) {
            $query->where('SubmissionCategory', $request->status);
        }

        if ($request->filled('agency')) {
            $query->whereHas('latestAssignment', function ($q) use ($request) {
                $q->where('AgencyID', $request->agency);
            });
        }


        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('SubmissionDate', [$request->start_date, $request->end_date]);
        }

        $inquiries = $query->orderByDesc('SubmissionDate')->get();
        $agencies = Agency::all();

        return view('InquiryFormSubmissionUI.MCMC.ListAllInquiryUI', compact('inquiries', 'agencies'));
    }



    public function m_AllDetailsInquiry($id)
    {
        $inquiry = Inquiry::with('latestAssignment.agency')
            ->where('InquiryID', $id)
            ->firstOrFail();

        $assignedAgency = InquiryAssignment::with(['agency', 'mcmc'])
            ->where('InquiryID', $id)
            ->latest('AssignDate')
            ->first();

        return view(
            'InquiryFormSubmissionUI.MCMC.AllDetailsInquiryUI',
            compact('inquiry', 'assignedAgency')
        );
    }
}
