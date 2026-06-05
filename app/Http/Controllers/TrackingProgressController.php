<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;  // Fixed import
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Exports\AgencyReportExport;


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

        return redirect()->route('agency.inquirylist')->with('success', 'Inquiry status saved.');
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
    // Get ALL inquiries with their progress and assignment details
    $inquiries = DB::table('inquiry')
        ->leftJoin('inquiryassignment', 'inquiry.InquiryID', '=', 'inquiryassignment.InquiryID')
        ->leftJoin('agency', 'inquiryassignment.AgencyID', '=', 'agency.AgencyID')
        ->leftJoin('inquiryprogress', function($join) {
            $join->on('inquiry.InquiryID', '=', 'inquiryprogress.InquiryID')
                 ->on('inquiryassignment.AgencyID', '=', 'inquiryprogress.AgencyID');
        })
        ->select(
            'inquiry.InquiryID',
            'inquiry.InquiryTitle',
            'inquiry.InquiryDescription',
            'inquiry.SubmissionDate',
            'inquiry.SubmissionStatus',
            'inquiryassignment.AssignDate',
            'agency.AgencyName',
            'inquiryprogress.VerificationStatus',
            'inquiryprogress.VerificationDateTime',
            'inquiryprogress.InvestigationBeginDate',
            'inquiryprogress.InvestigationDetails',
            'inquiryprogress.InvestigationDoc',
            'inquiryprogress.Notify'
        )
        ->orderByDesc('inquiry.SubmissionDate')
        ->get();

    // Group by InquiryID to handle multiple agencies (if any)
    $groupedInquiries = [];
    foreach ($inquiries as $row) {
        $id = $row->InquiryID;
        if (!isset($groupedInquiries[$id])) {
            $groupedInquiries[$id] = (object) [
                'InquiryID' => $row->InquiryID,
                'InquiryTitle' => $row->InquiryTitle,
                'InquiryDescription' => $row->InquiryDescription,
                'SubmissionDate' => $row->SubmissionDate,
                'SubmissionStatus' => $row->SubmissionStatus,
                'latestAssignment' => null,
                'latestProgress' => null,
            ];
        }
        
        // Track latest assignment (if exists)
        if ($row->AssignDate && !$groupedInquiries[$id]->latestAssignment) {
            $groupedInquiries[$id]->latestAssignment = (object) [
                'AssignDate' => $row->AssignDate,
                'agency' => (object) ['AgencyName' => $row->AgencyName]
            ];
        }
        
        // Track latest progress (if exists and not already set)
        if ($row->VerificationStatus && !$groupedInquiries[$id]->latestProgress) {
            $groupedInquiries[$id]->latestProgress = (object) [
                'VerificationStatus' => $row->VerificationStatus,
                'VerificationDateTime' => $row->VerificationDateTime,
                'InvestigationBeginDate' => $row->InvestigationBeginDate,
                'InvestigationDetails' => $row->InvestigationDetails,
                'InvestigationDoc' => $row->InvestigationDoc,
                'Notify' => $row->Notify
            ];
        } elseif ($row->InvestigationBeginDate && !$groupedInquiries[$id]->latestProgress) {
            $groupedInquiries[$id]->latestProgress = (object) [
                'VerificationStatus' => null,
                'VerificationDateTime' => null,
                'InvestigationBeginDate' => $row->InvestigationBeginDate,
                'InvestigationDetails' => $row->InvestigationDetails,
                'InvestigationDoc' => $row->InvestigationDoc,
                'Notify' => $row->Notify
            ];
        }
    }

    // Convert to array for view
    $inquiryList = array_values($groupedInquiries);

    return view('module4.monitor-progress', [
        'inquiries' => $inquiryList
    ]);
}

    ///////////////
    public function m_DisplayReport()
    {
        $agencies = DB::table('agency')->select('AgencyID', 'AgencyName')->get();
        return view('InquiryProgressTrackingUI.MCMC.ProgDisplayReportUI', compact('agencies'));
    }

    public function m_GenerateReport(Request $req)
    {
        $query = DB::table('inquiryassignment as ia')
            ->leftJoin('inquiryprogress as ip', 'ia.InquiryID', '=', 'ip.InquiryID')
            ->join('agency', 'ia.AgencyID', '=', 'agency.AgencyID');

        // Only apply date filter if both from and to dates are provided
        if ($req->from && $req->to) {
            $query->whereBetween('ia.AssignDate', [$req->from, $req->to]);
        }

        if ($req->agency) {
            $query->where('agency.AgencyID', $req->agency);
        }

        $rows = $query->select(
            'agency.AgencyName',
            'ia.InquiryID',
            'ip.VerificationStatus',
            'ia.AssignDate',
            'ip.InvestigationBeginDate',
            'ip.VerificationDateTime'
        )->get();

        // Apply filtering AFTER fetching everything
        $filteredRows = $rows;

        if ($req->status && $req->status !== 'all') {
            if ($req->status === 'Pending') {
                $filteredRows = $filteredRows->filter(
                    fn($r) => is_null($r->InvestigationBeginDate) && is_null($r->VerificationStatus)
                );
            } elseif ($req->status === 'Under Investigation') {
                $filteredRows = $filteredRows->filter(
                    fn($r) => $r->InvestigationBeginDate && is_null($r->VerificationStatus)
                );
            } else {
                $filteredRows = $filteredRows->filter(
                    fn($r) => $r->VerificationStatus === $req->status
                );
            }
        }

        // Enhanced report generation with better metrics - FIXED TO RETURN INTEGERS
        $report = $filteredRows->groupBy('AgencyName')->map(function ($group) {
            $assigned = $group->count();
            $resolved = $group->filter(
                fn($r) => in_array($r->VerificationStatus, ['Verified as True', 'Identified as Fake', 'Rejected'])
            )->count();
            $pending = $group->filter(
                fn($r) => is_null($r->InvestigationBeginDate) && is_null($r->VerificationStatus)
            )->count();
            $underInvestigation = $group->filter(
                fn($r) => $r->InvestigationBeginDate && is_null($r->VerificationStatus)
            )->count();

            // Calculate average time from assignment to resolution - FIXED TO RETURN INTEGER
            $resolvedInquiries = $group->filter(
                fn($r) => in_array($r->VerificationStatus, ['Verified as True', 'Identified as Fake', 'Rejected'])
                    && $r->VerificationDateTime && $r->AssignDate
            );

            $avgResolutionTime = 0;
            if ($resolvedInquiries->count() > 0) {
                $avgResolutionTime = (int) round($resolvedInquiries->map(function ($r) {
                    $assignDate = new \Carbon\Carbon($r->AssignDate);
                    $verificationDate = new \Carbon\Carbon($r->VerificationDateTime);
                    return abs($verificationDate->diffInDays($assignDate));
                })->average());
            }

            // Calculate average investigation time (from investigation start to completion) - FIXED TO RETURN INTEGER
            $avgInvestigationTime = 0;
            $investigationInquiries = $resolvedInquiries->filter(fn($r) => $r->InvestigationBeginDate);
            if ($investigationInquiries->count() > 0) {
                $avgInvestigationTime = (int) round($investigationInquiries->map(function ($r) {
                    $beginDate = new \Carbon\Carbon($r->InvestigationBeginDate);
                    $verificationDate = new \Carbon\Carbon($r->VerificationDateTime);
                    return abs($verificationDate->diffInDays($beginDate));
                })->average());
            }

            // Calculate pending delays (days since assignment for pending inquiries) - FIXED TO RETURN INTEGER
            $pendingDelays = $group->filter(
                fn($r) => is_null($r->InvestigationBeginDate) && is_null($r->VerificationStatus)
            )->map(function ($r) {
                $assignDate = new \Carbon\Carbon($r->AssignDate);
                return abs(now()->diffInDays($assignDate));
            });

            $avgPendingDelay = $pendingDelays->count() > 0 ? (int) round($pendingDelays->average()) : 0;
            $maxPendingDelay = $pendingDelays->count() > 0 ? (int) round($pendingDelays->max()) : 0;

            // Calculate investigation delays (days in investigation without resolution) - FIXED TO RETURN INTEGER
            $investigationDelays = $group->filter(
                fn($r) => $r->InvestigationBeginDate && is_null($r->VerificationStatus)
            )->map(function ($r) {
                $beginDate = new \Carbon\Carbon($r->InvestigationBeginDate);
                return abs(now()->diffInDays($beginDate));
            });

            $avgInvestigationDelay = $investigationDelays->count() > 0 ? (int) round($investigationDelays->average()) : 0;
            $maxInvestigationDelay = $investigationDelays->count() > 0 ? (int) round($investigationDelays->max()) : 0;

            // Resolution rate - keep as decimal for percentage
            $resolutionRate = $assigned > 0 ? round(($resolved / $assigned) * 100, 1) : 0;

            return compact(
                'assigned',
                'resolved',
                'pending',
                'underInvestigation',
                'avgResolutionTime',
                'avgInvestigationTime',
                'avgPendingDelay',
                'maxPendingDelay',
                'avgInvestigationDelay',
                'maxInvestigationDelay',
                'resolutionRate'
            );
        });

        // Calculate overall statistics
        $overallStats = [
            'totalAssigned' => $filteredRows->count(),
            'totalResolved' => $filteredRows->filter(
                fn($r) => in_array($r->VerificationStatus, ['Verified as True', 'Identified as Fake', 'Rejected'])
            )->count(),
            'totalPending' => $filteredRows->filter(
                fn($r) => is_null($r->InvestigationBeginDate) && is_null($r->VerificationStatus)
            )->count(),
            'totalUnderInvestigation' => $filteredRows->filter(
                fn($r) => $r->InvestigationBeginDate && is_null($r->VerificationStatus)
            )->count(),
        ];

        // Calculate critical delays (inquiries pending/investigating for more than X days)
        $criticalThreshold = 30; // days
        $criticalPending = $filteredRows->filter(function ($r) use ($criticalThreshold) {
            return is_null($r->InvestigationBeginDate) && is_null($r->VerificationStatus)
                && abs(now()->diffInDays(new \Carbon\Carbon($r->AssignDate))) > $criticalThreshold;
        });

        $criticalInvestigation = $filteredRows->filter(function ($r) use ($criticalThreshold) {
            return $r->InvestigationBeginDate && is_null($r->VerificationStatus)
                && abs(now()->diffInDays(new \Carbon\Carbon($r->InvestigationBeginDate))) > $criticalThreshold;
        });

        $agencies = DB::table('agency')->select('AgencyID', 'AgencyName')->get();

        return view('InquiryProgressTrackingUI.MCMC.ProgDisplayReportUI', [
            'report' => $report,
            'agencies' => $agencies,
            'req' => $req,
            'rows' => $filteredRows,
            'overallStats' => $overallStats,
            'criticalPending' => $criticalPending,
            'criticalInvestigation' => $criticalInvestigation,
            'criticalThreshold' => $criticalThreshold
        ]);
    }

    // Helper method to get enhanced report data - FIXED TO RETURN INTEGERS
    private function getReportData(Request $req)
    {
        $query = DB::table('inquiryassignment as ia')
            ->leftJoin('inquiryprogress as ip', 'ia.InquiryID', '=', 'ip.InquiryID')
            ->join('agency', 'ia.AgencyID', '=', 'agency.AgencyID');

        if ($req->from && $req->to) {
            $query->whereBetween('ia.AssignDate', [$req->from, $req->to]);
        }

        if ($req->agency) {
            $query->where('agency.AgencyID', $req->agency);
        }

        $rows = $query->select(
            'agency.AgencyName',
            'ia.InquiryID',
            'ip.VerificationStatus',
            'ia.AssignDate',
            'ip.InvestigationBeginDate',
            'ip.VerificationDateTime'
        )->get();

        // Apply filtering
        $filteredRows = $rows;
        if ($req->status && $req->status !== 'all') {
            if ($req->status === 'Pending') {
                $filteredRows = $filteredRows->filter(
                    fn($r) => is_null($r->InvestigationBeginDate) && is_null($r->VerificationStatus)
                );
            } elseif ($req->status === 'Under Investigation') {
                $filteredRows = $filteredRows->filter(
                    fn($r) => $r->InvestigationBeginDate && is_null($r->VerificationStatus)
                );
            } else {
                $filteredRows = $filteredRows->filter(
                    fn($r) => $r->VerificationStatus === $req->status
                );
            }
        }

        // Generate enhanced report data - FIXED TO RETURN INTEGERS
        $report = $filteredRows->groupBy('AgencyName')->map(function ($group, $agencyName) {
            $assigned = $group->count();
            $resolved = $group->filter(
                fn($r) => in_array($r->VerificationStatus, ['Verified as True', 'Identified as Fake', 'Rejected'])
            )->count();
            $pending = $group->filter(
                fn($r) => is_null($r->InvestigationBeginDate) && is_null($r->VerificationStatus)
            )->count();
            $underInvestigation = $group->filter(
                fn($r) => $r->InvestigationBeginDate && is_null($r->VerificationStatus)
            )->count();

            // Enhanced time calculations - FIXED TO RETURN INTEGERS
            $resolvedInquiries = $group->filter(
                fn($r) => in_array($r->VerificationStatus, ['Verified as True', 'Identified as Fake', 'Rejected'])
                    && $r->VerificationDateTime && $r->AssignDate
            );

            $avgResolutionTime = 0;
            if ($resolvedInquiries->count() > 0) {
                $avgResolutionTime = (int) round($resolvedInquiries->map(function ($r) {
                    $assignDate = new \Carbon\Carbon($r->AssignDate);
                    $verificationDate = new \Carbon\Carbon($r->VerificationDateTime);
                    return abs($verificationDate->diffInDays($assignDate));
                })->average());
            }

            $avgPendingDelay = 0;
            $pendingInquiries = $group->filter(
                fn($r) => is_null($r->InvestigationBeginDate) && is_null($r->VerificationStatus)
            );
            if ($pendingInquiries->count() > 0) {
                $avgPendingDelay = (int) round($pendingInquiries->map(function ($r) {
                    $assignDate = new \Carbon\Carbon($r->AssignDate);
                    return abs(now()->diffInDays($assignDate));
                })->average());
            }

            $resolutionRate = $assigned > 0 ? round(($resolved / $assigned) * 100, 1) : 0;

            return [
                'agency' => $agencyName,
                'assigned' => $assigned,
                'resolved' => $resolved,
                'pending' => $pending,
                'underInvestigation' => $underInvestigation,
                'avgResolutionTime' => $avgResolutionTime, // Now guaranteed to be integer
                'avgPendingDelay' => $avgPendingDelay, // Now guaranteed to be integer
                'resolutionRate' => $resolutionRate
            ];
        });

        // Calculate overall statistics
        $overallStats = [
            'totalAssigned' => $filteredRows->count(),
            'totalResolved' => $filteredRows->filter(
                fn($r) => in_array($r->VerificationStatus, ['Verified as True', 'Identified as Fake', 'Rejected'])
            )->count(),
            'totalPending' => $filteredRows->filter(
                fn($r) => is_null($r->InvestigationBeginDate) && is_null($r->VerificationStatus)
            )->count(),
            'totalUnderInvestigation' => $filteredRows->filter(
                fn($r) => $r->InvestigationBeginDate && is_null($r->VerificationStatus)
            )->count(),
        ];

        return [
            'report' => $report->values()->toArray(),
            'overallStats' => $overallStats,
            'filteredRows' => $filteredRows
        ];
    }

    // Fixed Excel Export with Chart Data
    public function m_ExportExcel(Request $req)
    {
        $data = $this->getReportData($req);

        return Excel::download(new AgencyReportExport([
            'report' => $data['report'],
            'overallStats' => $data['overallStats'],
            'filteredRows' => $data['filteredRows'],
            'req' => $req,
            'chartData' => [
                'labels' => collect($data['report'])->pluck('agency')->toArray(),
                'assigned' => collect($data['report'])->pluck('assigned')->toArray(),
                'resolved' => collect($data['report'])->pluck('resolved')->toArray(),
                'pending' => collect($data['report'])->pluck('pending')->toArray(),
                'underInvestigation' => collect($data['report'])->pluck('underInvestigation')->toArray(),
            ]
        ]), 'agency_report.xlsx');
    }

    // Fixed PDF Export with Chart
    public function m_ExportPDF(Request $req)
    {
        $data = $this->getReportData($req);
        $agencies = DB::table('agency')->select('AgencyID', 'AgencyName')->get();

        // Convert report data to the format your view expects
        $report = collect($data['report'])->keyBy('agency')->map(function ($item) {
            return [
                'assigned' => $item['assigned'],
                'resolved' => $item['resolved'],
                'pending' => $item['pending'],
                'underInvestigation' => $item['underInvestigation'],
                'avgResolutionTime' => $item['avgResolutionTime'], // Already integer
                'avgPendingDelay' => $item['avgPendingDelay'], // Already integer
                'resolutionRate' => $item['resolutionRate']
            ];
        });

        // Generate chart as base64 image for PDF
        $chartData = [
            'labels' => collect($data['report'])->pluck('agency')->toArray(),
            'assigned' => collect($data['report'])->pluck('assigned')->toArray(),
            'resolved' => collect($data['report'])->pluck('resolved')->toArray(),
            'pending' => collect($data['report'])->pluck('pending')->toArray(),
            'underInvestigation' => collect($data['report'])->pluck('underInvestigation')->toArray(),
        ];

        $pdf = Pdf::loadView('InquiryProgressTrackingUI.MCMC.ProgDisplayReportUI', [
            'report' => $report,
            'agencies' => $agencies,
            'req' => $req,
            'rows' => $data['filteredRows'],
            'overallStats' => $data['overallStats'],
            'chartData' => $chartData,
            'isPDF' => true
        ]);

        return $pdf->download('agency_report.pdf');
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
