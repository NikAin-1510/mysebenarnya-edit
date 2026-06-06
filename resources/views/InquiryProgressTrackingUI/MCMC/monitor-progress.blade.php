@extends('layouts.layout')

@section('page-name', 'Monitor Progress')

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
    .filter-form {
        display: flex;
        gap: 15px;
        margin-bottom: 25px;
        flex-wrap: wrap;
        align-items: center;
    }
    .filter-form input, .filter-form select {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    .filter-form button {
        background: rgb(104, 75, 142);
        color: white;
        padding: 8px 20px;
        border-radius: 5px;
        border: none;
        cursor: pointer;
    }
    .filter-form .btn-reset {
        background: #6c757d;
        color: white;
        padding: 8px 15px;
        border-radius: 5px;
        text-decoration: none;
    }
    .inquiries-section {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    .inquiry-card {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: box-shadow 0.2s;
    }
    .inquiry-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .inquiry-info {
        flex: 1;
    }
    .inquiry-title {
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 5px;
    }
    .inquiry-meta {
        font-size: 13px;
        color: #6c757d;
    }
    .inquiry-status {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: bold;
        margin-left: 10px;
    }
    .status-pending { background: #ffc107; color: #333; }
    .status-investigation { background: #17a2b8; color: white; }
    .status-completed { background: #28a745; color: white; }
    .status-rejected { background: #dc3545; color: white; }
    .btn-view-progress {
        background: rgb(104, 75, 142);
        color: white;
        padding: 8px 20px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 13px;
    }
    .btn-view-progress:hover {
        background: #5a3d7a;
        color: white;
    }
</style>

<div class="container">
    <div class="dashboard-header">
        <h1>Monitor Inquiry Progress</h1>
        <p>View and track the status of each inquiry</p>
    </div>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('monitor.progress') }}" class="filter-form">
        <input type="date" name="start_date" value="{{ request('start_date') }}" placeholder="Start Date">
        <input type="date" name="end_date" value="{{ request('end_date') }}" placeholder="End Date">
        <select name="status">
            <option value="">All Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Assignment</option>
            <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned to Agency</option>
            <option value="investigation" {{ request('status') == 'investigation' ? 'selected' : '' }}>Under Investigation</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed (Verified)</option>
            <option value="fake" {{ request('status') == 'fake' ? 'selected' : '' }}>Identified as Fake</option>
        </select>
        <button type="submit"><i class="fas fa-search"></i> Filter</button>
        <a href="{{ route('monitor.progress') }}" class="btn-reset">Reset</a>
    </form>

    {{-- List of Inquiries --}}
    <div class="inquiries-section">
        @forelse($inquiries as $inquiry)
            @php
                $progress = $inquiry->latestProgress ?? null;
                $assignment = $inquiry->latestAssignment ?? null;
                
                if ($progress && $progress->VerificationStatus === 'Verified as True') {
                    $statusLabel = 'Verified as True';
                    $statusClass = 'status-completed';
                } elseif ($progress && $progress->VerificationStatus === 'Identified as Fake') {
                    $statusLabel = 'Identified as Fake';
                    $statusClass = 'status-rejected';
                } elseif ($progress && $progress->InvestigationBeginDate) {
                    $statusLabel = 'Under Investigation';
                    $statusClass = 'status-investigation';
                } elseif ($assignment) {
                    $statusLabel = 'Assigned to Agency';
                    $statusClass = 'status-investigation';
                } else {
                    $statusLabel = 'Pending Assignment';
                    $statusClass = 'status-pending';
                }
            @endphp

            <div class="inquiry-card">
                <div class="inquiry-info">
                    <div class="inquiry-title">
                        {{ $inquiry->InquiryTitle }}
                        <span class="inquiry-status {{ $statusClass }}">{{ $statusLabel }}</span>
                    </div>
                    <div class="inquiry-meta">
                        ID: {{ $inquiry->InquiryID }} | 
                        Submitted: {{ date('d M Y', strtotime($inquiry->SubmissionDate)) }}
                        @if($assignment && $assignment->agency)
                            | Agency: {{ $assignment->agency->AgencyName }}
                        @endif
                    </div>
                </div>
                <a href="{{ route('monitor.progress.detail', $inquiry->InquiryID) }}" class="btn-view-progress">
                    View Progress →
                </a>
            </div>
        @empty
            <div class="inquiry-card" style="justify-content: center;">
                <p>No inquiries found.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection