@extends('layouts.layout')

@section('page-name', 'Inquiry Progress')

@section('content')
<style>
    .dashboard-header {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 25px;
    }
    .dashboard-header h1 {
        margin: 0 0 5px 0;
        color: #333;
        font-size: 22px;
    }
    .dashboard-header p {
        margin: 0;
        color: #6c757d;
    }
    .progress-container {
        background: white;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        padding: 25px;
    }
    .inquiry-info {
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    .inquiry-title {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 8px;
    }
    .progress-item {
        display: flex;
        margin-bottom: 20px;
        padding: 12px 0;
        border-bottom: 1px dashed #eee;
    }
    .progress-icon {
        width: 30px;
        margin-right: 15px;
        text-align: center;
    }
    .progress-content {
        flex: 1;
    }
    .progress-step {
        font-weight: bold;
        font-size: 15px;
        margin-bottom: 5px;
    }
    .progress-date {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 5px;
    }
    .progress-detail {
        font-size: 13px;
        color: #555;
    }
    .completed { color: #28a745; }
    .pending { color: #ffc107; }
    .upcoming { color: #6c757d; }
    .btn-back {
        background: rgb(104, 75, 142);
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        display: inline-block;
        margin-top: 20px;
    }
    .btn-back:hover {
        background: #5a3d7a;
        color: white;
    }
</style>

<div class="container">
    <div class="dashboard-header">
        <h1>Inquiry Progress</h1>
        <p>{{ $inquiry->InquiryTitle }} (ID: {{ $inquiry->InquiryID }})</p>
    </div>

    <div class="progress-container">
        <div class="inquiry-info">
            <div class="inquiry-title">{{ $inquiry->InquiryTitle }}</div>
            <div>Submitted: {{ date('d M Y', strtotime($inquiry->SubmissionDate)) }}</div>
            @if($assignment && $assignment->AgencyName)
                <div>Assigned to: {{ $assignment->AgencyName }}</div>
            @endif
        </div>

        {{-- Progress Timeline --}}
        <div class="progress-item">
            <div class="progress-icon">📋</div>
            <div class="progress-content">
                <div class="progress-step completed">Submitted</div>
                <div class="progress-date">{{ date('d M Y', strtotime($inquiry->SubmissionDate)) }}</div>
                <div class="progress-detail">Inquiry has been submitted by public user</div>
            </div>
        </div>

        <div class="progress-item">
            <div class="progress-icon">📌</div>
            <div class="progress-content">
                @if($assignment)
                    <div class="progress-step completed">Assigned to Agency</div>
                    <div class="progress-date">{{ date('d M Y', strtotime($assignment->AssignDate)) }}</div>
                    <div class="progress-detail">Assigned to: {{ $assignment->AgencyName }}</div>
                @else
                    <div class="progress-step pending">Assigned to Agency</div>
                    <div class="progress-detail">Waiting for MCMC to assign to agency</div>
                @endif
            </div>
        </div>

        <div class="progress-item">
            <div class="progress-icon">🔍</div>
            <div class="progress-content">
                @if($progress && $progress->InvestigationBeginDate)
                    <div class="progress-step completed">Investigation Started</div>
                    <div class="progress-date">{{ date('d M Y', strtotime($progress->InvestigationBeginDate)) }}</div>
                    @if($progress->InvestigationDetails)
                        <div class="progress-detail">{{ $progress->InvestigationDetails }}</div>
                    @endif
                @elseif($assignment)
                    <div class="progress-step pending">Investigation</div>
                    <div class="progress-detail">Waiting for agency to start investigation</div>
                @else
                    <div class="progress-step upcoming">Investigation</div>
                    <div class="progress-detail">Pending assignment first</div>
                @endif
            </div>
        </div>

        <div class="progress-item">
            <div class="progress-icon">📝</div>
            <div class="progress-content">
                @if($progress && $progress->VerificationStatus)
                    @if($progress->VerificationStatus === 'Verified as True')
                        <div class="progress-step completed">Final Verdict: Verified as True</div>
                        <div class="progress-date">{{ date('d M Y', strtotime($progress->VerificationDateTime)) }}</div>
                        <div class="progress-detail">Case closed - Information verified as true</div>
                    @elseif($progress->VerificationStatus === 'Identified as Fake')
                        <div class="progress-step completed">Final Verdict: Identified as Fake</div>
                        <div class="progress-date">{{ date('d M Y', strtotime($progress->VerificationDateTime)) }}</div>
                        <div class="progress-detail">Case closed - Information identified as fake</div>
                    @elseif($progress->VerificationStatus === 'Rejected')
                        <div class="progress-step completed">Final Verdict: Rejected</div>
                        <div class="progress-date">{{ date('d M Y', strtotime($progress->VerificationDateTime)) }}</div>
                        <div class="progress-detail">Case rejected - Insufficient evidence</div>
                    @endif
                @else
                    <div class="progress-step upcoming">Final Verdict</div>
                    <div class="progress-detail">Pending investigation completion</div>
                @endif
            </div>
        </div>
    </div>

    <div style="text-align: center;">
        <a href="{{ route('monitor.progress') }}" class="btn-back">← Back to List</a>
    </div>
</div>
@endsection