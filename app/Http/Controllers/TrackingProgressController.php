<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;  // Fixed import
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Exports\AgencyReportExport;


class TrackingProgressController extends Controller
{
    public function a_InquiryList()
    {
        return view('InquiryProgressTrackingUI.Agency.ListAssignedInquiryUI');
    }

    public function a_UpdateStatus()
    {
        return view('InquiryProgressTrackingUI.Agency.UpdateStatusUI');
    }

    public function m_CreateReport()
    {
        return view('InquiryProgressTrackingUI.MCMC.CreateReportUI');
    }

    public function m_InquiryDetails()
    {
        return view('InquiryProgressTrackingUI.MCMC.MonitorProgressUI');
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

        // Enhanced report generation with better metrics
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

            // Calculate average time from assignment to resolution - FIXED
            $resolvedInquiries = $group->filter(
                fn($r) => in_array($r->VerificationStatus, ['Verified as True', 'Identified as Fake', 'Rejected'])
                    && $r->VerificationDateTime && $r->AssignDate
            );

            $avgResolutionTime = 0;
            if ($resolvedInquiries->count() > 0) {
                $avgResolutionTime = round($resolvedInquiries->map(function ($r) {
                    $assignDate = new \Carbon\Carbon($r->AssignDate);
                    $verificationDate = new \Carbon\Carbon($r->VerificationDateTime);
                    return abs($verificationDate->diffInDays($assignDate)); // Use abs() to ensure positive
                })->average()); // Round to integer
            }

            // Calculate average investigation time (from investigation start to completion) - FIXED
            $avgInvestigationTime = 0;
            $investigationInquiries = $resolvedInquiries->filter(fn($r) => $r->InvestigationBeginDate);
            if ($investigationInquiries->count() > 0) {
                $avgInvestigationTime = round($investigationInquiries->map(function ($r) {
                    $beginDate = new \Carbon\Carbon($r->InvestigationBeginDate);
                    $verificationDate = new \Carbon\Carbon($r->VerificationDateTime);
                    return abs($verificationDate->diffInDays($beginDate)); // Use abs() to ensure positive
                })->average()); // Round to integer
            }

            // Calculate pending delays (days since assignment for pending inquiries) - FIXED
            $pendingDelays = $group->filter(
                fn($r) => is_null($r->InvestigationBeginDate) && is_null($r->VerificationStatus)
            )->map(function ($r) {
                $assignDate = new \Carbon\Carbon($r->AssignDate);
                return abs(now()->diffInDays($assignDate)); // Use abs() to ensure positive
            });

            $avgPendingDelay = $pendingDelays->count() > 0 ? round($pendingDelays->average()) : 0;
            $maxPendingDelay = $pendingDelays->count() > 0 ? round($pendingDelays->max()) : 0;

            // Calculate investigation delays (days in investigation without resolution) - FIXED
            $investigationDelays = $group->filter(
                fn($r) => $r->InvestigationBeginDate && is_null($r->VerificationStatus)
            )->map(function ($r) {
                $beginDate = new \Carbon\Carbon($r->InvestigationBeginDate);
                return abs(now()->diffInDays($beginDate)); // Use abs() to ensure positive
            });

            $avgInvestigationDelay = $investigationDelays->count() > 0 ? round($investigationDelays->average()) : 0;
            $maxInvestigationDelay = $investigationDelays->count() > 0 ? round($investigationDelays->max()) : 0;

            // Resolution rate
            $resolutionRate = $assigned > 0 ? round(($resolved / $assigned) * 100, 2) : 0;

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

    // Helper method to get enhanced report data - FIXED
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

        // Generate enhanced report data - FIXED
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

            // Enhanced time calculations - FIXED
            $resolvedInquiries = $group->filter(
                fn($r) => in_array($r->VerificationStatus, ['Verified as True', 'Identified as Fake', 'Rejected'])
                    && $r->VerificationDateTime && $r->AssignDate
            );

            $avgResolutionTime = 0;
            if ($resolvedInquiries->count() > 0) {
                $avgResolutionTime = round($resolvedInquiries->map(function ($r) {
                    $assignDate = new \Carbon\Carbon($r->AssignDate);
                    $verificationDate = new \Carbon\Carbon($r->VerificationDateTime);
                    return abs($verificationDate->diffInDays($assignDate)); // Use abs() to ensure positive
                })->average()); // Round to integer
            }

            $avgPendingDelay = 0;
            $pendingInquiries = $group->filter(
                fn($r) => is_null($r->InvestigationBeginDate) && is_null($r->VerificationStatus)
            );
            if ($pendingInquiries->count() > 0) {
                $avgPendingDelay = round($pendingInquiries->map(function ($r) {
                    $assignDate = new \Carbon\Carbon($r->AssignDate);
                    return abs(now()->diffInDays($assignDate)); // Use abs() to ensure positive
                })->average()); // Round to integer
            }

            $resolutionRate = $assigned > 0 ? round(($resolved / $assigned) * 100, 2) : 0;

            return [
                'agency' => $agencyName,
                'assigned' => $assigned,
                'resolved' => $resolved,
                'pending' => $pending,
                'underInvestigation' => $underInvestigation,
                'avgResolutionTime' => $avgResolutionTime, // Already rounded to integer
                'avgPendingDelay' => $avgPendingDelay, // Already rounded to integer
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
                'avgResolutionTime' => $item['avgResolutionTime'],
                'avgPendingDelay' => $item['avgPendingDelay'],
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

    public function m_InquiryList()
    {
        return view('InquiryProgressTrackingUI.MCMC.ProgListInquiryUI');
    }

    public function p_OwnInquiryDetails()
    {
        return view('InquiryProgressTrackingUI.Public.DetailsOwnInquiryUI');
    }

    public function p_ListAllInquiry(Request $request)
    {
        $status = $request->input('status');
        $ownOnly = $request->has('own_only');
        $publicId = $request->input('public_id'); // Optional, based on checkbox

        $query = Inquiry::with(['progress', 'latestAssignment']);

        // Filter by VerificationStatus or InvestigationBeginDate via relation
        if (!empty($status)) {
            if ($status === 'Under Investigation') {
                $query->whereHas('progress', function ($q) {
                    $q->whereNotNull('InvestigationBeginDate')
                        ->whereNull('VerificationStatus');
                });
            } elseif (in_array($status, ['Verified as True', 'Identified as Fake'])) {
                $query->whereHas('progress', function ($q) use ($status) {
                    $q->where('VerificationStatus', $status);
                });
            }
        }

        // Filter by user's own submissions
        if ($ownOnly && !empty($publicId)) {
            $query->where('PublicID', $publicId);
        }

        $inquiries = $query->orderByDesc('SubmissionDate')->get();

        return view('InquiryProgressTrackingUI.Public.ListAllInquiryUI', compact('inquiries'));
    }


    public function p_NotificationDetails()
    {
        return view('InquiryProgressTrackingUI.Public.NotificationDetailsUI');
    }

    public function p_NotificationList()
    {
        return view('InquiryProgressTrackingUI.Public.NotificationListUI');
    }



    public function p_OwnInquiryList()
    {
        return view('InquiryProgressTrackingUI.Public.OwnListInquiryUI');
    }

    public function p_InquiryDetails()
    {
        return view('InquiryProgressTrackingUI.Public.ProgInquiryDetailsUI');
    }
}
