<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response; // ✅ Add this line
use App\Models\Inquiry;
use Illuminate\Support\Str;


class InquiryController extends Controller
{
    // === AGENCY ===
    public function a_DisplayReport()
    {
        return view('InquiryFormSubmissionUI.Agency.DisplayReportUI');
    }

    public function a_HistoryInquiry()
    {
        return view('InquiryFormSubmissionUI.Agency.HistoryInquiryUI');
    }

    public function a_ListAssignedInquiry()
    {
        return view('InquiryFormSubmissionUI.Agency.ListAssignedInquiryUI');
    }

    public function a_ReviewInquiry()
    {
        return view('InquiryFormSubmissionUI.Agency.ReviewInquiryUI');
    }

    // === MCMC ===
    public function m_ListInquiry()
    {
        return view('InquiryFormSubmissionUI.MCMC.ListInquiryUI');
    }

    public function m_FilteredInquiry()
    {
        return view('InquiryFormSubmissionUI.MCMC.FilteredInquiryUI'); // fix path if needed
    }
    public function index(Request $request)
    {
        $agencies = DB::table('agency')->get();

        $query = DB::table('inquiry')
            ->leftJoin('inquiryassignment', 'inquiry.InquiryID', '=', 'inquiryassignment.InquiryID')
            ->leftJoin('agency', 'inquiryassignment.AgencyID', '=', 'agency.AgencyID')
            ->select('inquiry.*', 'agency.AgencyName');

        if ($request->date) {
            $query->whereDate('inquiry.SubmissionDate', $request->date);
        }

        if ($request->status) {
            $query->where('inquiry.SubmissionStatus', $request->status);
        }

        if ($request->agency) {
            $query->where('agency.AgencyID', $request->agency);
        }

        $inquiries = $query->orderBy('SubmissionDate', 'desc')->get();

        return view('InquiryFormSubmissionUI.MCMC.PreviousInquiriesUI', compact('inquiries', 'agencies'));
    }

    public function downloadFilteredInquiries(Request $request)
    {
        $query = DB::table('inquiry')
            ->leftJoin('inquiryassignment', 'inquiry.InquiryID', '=', 'inquiryassignment.InquiryID')
            ->leftJoin('agency', 'inquiryassignment.AgencyID', '=', 'agency.AgencyID')
            ->select('inquiry.InquiryTitle', 'inquiry.InquiryDescription', 'inquiry.SubmissionDate', 'agency.AgencyName', 'inquiry.SubmissionStatus');

        if ($request->date) {
            $query->whereDate('inquiry.SubmissionDate', $request->date);
        }

        if ($request->status) {
            $query->where('inquiry.SubmissionStatus', $request->status);
        }

        if ($request->agency) {
            $query->where('agency.AgencyID', $request->agency);
        }

        $results = $query->get();

        $csvData = [];
        $csvData[] = ['Inquiry Title', 'Description', 'Submission Date', 'Agency', 'Status'];

        foreach ($results as $row) {
            $csvData[] = [
                $row->InquiryTitle,
                $row->InquiryDescription,
                \Carbon\Carbon::parse($row->SubmissionDate)->format('d M Y'),
                $row->AgencyName ?? 'Unassigned',
                ucfirst($row->SubmissionStatus),
            ];
        }

        $filename = 'filtered_inquiries_' . now()->format('Ymd_His') . '.csv';
        $handle = fopen('php://temp', 'r+');
        foreach ($csvData as $line) {
            fputcsv($handle, $line);
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return Response::make($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
    public function showPreviousInquiries(Request $request)
    {
        $query = DB::table('inquiry')
            ->leftJoin('inquiryassignment', 'inquiry.InquiryID', '=', 'inquiryassignment.InquiryID')
            ->leftJoin('agency', 'inquiryassignment.AgencyID', '=', 'agency.AgencyID')
            ->select('inquiry.*', 'agency.AgencyName', 'inquiry.SubmissionStatus');

        if ($request->date) {
            $query->whereDate('inquiry.SubmissionDate', $request->date);
        }

        if ($request->status) {
            $query->where('inquiryassignment.Status', $request->status);
        }

        if ($request->agency) {
            $query->where('agency.AgencyName', $request->agency);
        }

        $inquiries = $query->orderBy('inquiry.SubmissionDate', 'desc')->get();
        $agencies = DB::table('agency')->get();

        return view('InquiryFormSubmissionUI.MCMC.FilteredInquiryUI', compact('inquiries', 'agencies'));
    }


    public function m_DetailsInquiry()
    {
        return view('InquiryFormSubmissionUI.MCMC.DetailsInquiryUI'); // fix if reused
    }

    public function m_ReviewInquiry(Request $request)
    {
        $query = DB::table('inquiry')
            ->leftJoin('inquiryassignment', 'inquiry.InquiryID', '=', 'inquiryassignment.InquiryID')
            ->leftJoin('agency', 'inquiryassignment.AgencyID', '=', 'agency.AgencyID')
            ->select('inquiry.*', 'agency.AgencyName');

        if ($request->month) {
            $query->whereMonth('inquiry.SubmissionDate', $request->month);
        }

        if ($request->year) {
            $query->whereYear('inquiry.SubmissionDate', $request->year);
        }

        if ($request->agency) {
            $query->where('agency.AgencyID', $request->agency);
        }

        $inquiries = $query->orderBy('inquiry.SubmissionDate', 'desc')->get();
        $agencies = DB::table('agency')->get();

        return view('InquiryFormSubmissionUI.MCMC.ReviewInquiry', compact('inquiries', 'agencies'));
    }

    // === PUBLIC ===
    public function p_ListInquiry()
    {
        return view('InquiryFormSubmissionUI.Public.ListInquiryUI');
    }

    public function p_DetailsOwnInquiry()
    {
        return view('InquiryFormSubmissionUI.Public.DetailsOwnInquiryUI');
    }

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:20',
            'details' => 'required|string|max:30',
            'type_link' => 'required|url|max:255',
            'evidence' => 'nullable|file|max:5120',
        ]);

        $inquiryID = 'IQ' . str_pad(DB::table('inquiries')->count() + 1, 6, '0', STR_PAD_LEFT);

        $filePath = null;

        if ($request->hasFile('evidence')) {
            $filePath = $request->file('evidence')->store('evidence', 'public');
        }

        DB::table('inquiries')->insert([
            'InquiryID' => $inquiryID,
            'PublicID' => Auth::id(),
            'InquiryTitle' => $validated['subject'],
            'InquiryDescription' => $validated['details'],
            'SubmissionDate' => now(),
            'SubmissionStatus' => 'Pending',
            'SubmissionLink' => $validated['type_link'],
            'SubmissionEvidence' => $filePath ? basename($filePath) : null,
        ]);

        return redirect()->back()->with('success', 'Form successfully submitted!');
    }
}
