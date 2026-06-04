@extends('layouts.layout')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/module4/mcmc.css') }}">
@endsection

@section('page-name', 'Monitor Progress')

@section('content')
<style>
    .progress-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px;
    }
    .progress-header {
        background: rgb(104, 75, 142);
        color: white;
        padding: 15px 20px;
        border-radius: 10px 10px 0 0;
        margin-bottom: 20px;
    }
    .inquiry-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 25px;
        overflow: hidden;
    }
    .inquiry-title {
        background: #f8f9fa;
        padding: 12px 20px;
        border-left: 4px solid rgb(104, 75, 142);
        font-weight: bold;
        border-bottom: 1px solid #eee;
    }
    .timeline {
        padding: 20px;
    }
    .timeline-item {
        display: flex;
        margin-bottom: 20px;
        position: relative;
    }
    .timeline-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-right: 15px;
        flex-shrink: 0;
    }
    .timeline-icon.completed {
        background: #28a745;
        color: white;
    }
    .timeline-icon.pending {
        background: #ffc107;
        color: #333;
    }
    .timeline-icon.upcoming {
        background: #e9ecef;
        color: #6c757d;
    }
    .timeline-content {
        flex: 1;
        padding-bottom: 15px;
        border-bottom: 1px dashed #e9ecef;
    }
    .timeline-status {
        font-weight: bold;
        font-size: 15px;
    }
    .timeline-date {
        font-size: 12px;
        color: #6c757d;
        margin-top: 4px;
    }
    .timeline-detail {
        font-size: 13px;
        color: #555;
        margin-top: 5px;
    }
    .agency-badge {
        background: #e9ecef;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 12px;
        display: inline-block;
    }
    .verdict-true {
        color: #28a745;
        font-weight: bold;
    }
    .verdict-fake {
        color: #dc3545;
        font-weight: bold;
    }
    .no-data {
        text-align: center;
        padding: 40px;
        color: #6c757d;
    }
    .btn-back {
        background: rgb(104, 75, 142);
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        display: inline-block;
        margin-top: 10px;
    }
    .btn-back:hover {
        background: #5a3d7a;
        color: white;
    }
</style>

<div class="progress-container">
    <div class="progress-header">
        <h2>📊 Monitor Inquiry Progress</h2>
        <p>Track the status of each inquiry from submission to final verdict</p>
    </div>

    @forelse($inquiries as $inquiry)
        @php
            $progress = $inquiry->latestProgress;
            $assignment = $inquiry->latestAssignment;
            
            $submittedDate = date('d M Y', strtotime($inquiry->SubmissionDate));
            $assignedDate = $assignment ? date('d M Y', strtotime($assignment->AssignDate)) : null;
            $agencyName = $assignment->agency->AgencyName ?? null;
            $investigationDate = $progress->InvestigationBeginDate ?? null;
            $verificationDate = $progress->VerificationDateTime ?? null;
            $verificationStatus = $progress->VerificationStatus ?? null;
            $investigationDetails = $progress->InvestigationDetails ?? null;
            
            // Determine step statuses
            $step1Completed = true;
            $step2Completed = !is_null($assignedDate);
            $step3Completed = !is_null($investigationDate);
            $step4Completed = !is_null($verificationStatus);
            
            // Verdict text
            if ($verificationStatus === 'Verified as True') {
                $verdictText = 'VERIFIED AS TRUE - Case Closed';
                $verdictClass = 'verdict-true';
            } elseif ($verificationStatus === 'Identified as Fake') {
                $verdictText = 'IDENTIFIED AS FAKE - Case Closed';
                $verdictClass = 'verdict-fake';
            } elseif ($verificationStatus === 'Rejected') {
                $verdictText = 'REJECTED - Insufficient Evidence';
                $verdictClass = 'verdict-fake';
            } else {
                $verdictText = 'Pending Final Verdict';
                $verdictClass = '';
            }
        @endphp

        <div class="inquiry-card">
            <div class="inquiry-title">
                📋 {{ $inquiry->InquiryTitle }}
                <span style="float: right; font-size: 12px; font-weight: normal; color: #6c757d;">ID: {{ $inquiry->InquiryID }}</span>
            </div>
            
            <div class="timeline">
                {{-- Step 1: Submitted --}}
                <div class="timeline-item">
                    <div class="timeline-icon completed">1</div>
                    <div class="timeline-content">
                        <div class="timeline-status">✅ Submitted</div>
                        <div class="timeline-date">{{ $submittedDate }}</div>
                        <div class="timeline-detail">Inquiry has been submitted by public user</div>
                    </div>
                </div>

                {{-- Step 2: Assigned to Agency --}}
                <div class="timeline-item">
                    <div class="timeline-icon {{ $step2Completed ? 'completed' : 'upcoming' }}">
                        {{ $step2Completed ? '2' : '2' }}
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-status">
                            @if($step2Completed)
                                ✅ Assigned to Agency
                            @else
                                ⏳ Assigned to Agency (Pending)
                            @endif
                        </div>
                        @if($step2Completed)
                            <div class="timeline-date">{{ $assignedDate }}</div>
                            <div class="timeline-detail">Assigned to: <span class="agency-badge">{{ $agencyName }}</span></div>
                        @else
                            <div class="timeline-detail">Waiting for MCMC to assign to agency</div>
                        @endif
                    </div>
                </div>

                {{-- Step 3: Investigation --}}
                <div class="timeline-item">
                    <div class="timeline-icon {{ $step3Completed ? 'completed' : ($step2Completed ? 'pending' : 'upcoming') }}">
                        3
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-status">
                            @if($step3Completed)
                                🔍 Investigation Started
                            @elseif($step2Completed)
                                ⏳ Investigation (Pending)
                            @else
                                ⏳ Investigation (Pending)
                            @endif
                        </div>
                        @if($step3Completed)
                            <div class="timeline-date">Started: {{ date('d M Y', strtotime($investigationDate)) }}</div>
                        @endif
                        @if($investigationDetails)
                            <div class="timeline-detail">{{ $investigationDetails }}</div>
                        @endif
                    </div>
                </div>

                {{-- Step 4: Final Verdict --}}
                <div class="timeline-item">
                    <div class="timeline-icon {{ $step4Completed ? 'completed' : 'upcoming' }}">4</div>
                    <div class="timeline-content">
                        <div class="timeline-status">
                            @if($step4Completed)
                                @if($verificationStatus === 'Verified as True')
                                    ✅ {{ $verdictText }}
                                @elseif($verificationStatus === 'Identified as Fake')
                                    ❌ {{ $verdictText }}
                                @else
                                    ⛔ {{ $verdictText }}
                                @endif
                            @else
                                ⏳ {{ $verdictText }}
                            @endif
                        </div>
                        @if($verificationDate)
                            <div class="timeline-date">Completed: {{ date('d M Y', strtotime($verificationDate)) }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="inquiry-card">
            <div class="inquiry-title">No Inquiries Found</div>
            <div class="no-data">
                <p>No inquiries have been submitted yet.</p>
                <a href="{{ route('mcmc.all.inquiry') }}" class="btn-back">Go to List Inquiry</a>
            </div>
        </div>
    @endforelse
</div>

<div style="text-align: center; margin-top: 20px;">
    <a href="{{ route('mcmc.all.inquiry') }}" class="btn-back">← Back to List Inquiry</a>
</div>
@endsection
