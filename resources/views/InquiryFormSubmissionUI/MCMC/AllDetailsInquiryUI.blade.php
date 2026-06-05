@extends('layouts.layout')

@section('content')
<style>
    .inquiry-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 20px;
    }

    .inquiry-header {
        background: rgb(104, 75, 142);
        color: white;
        padding: 15px 20px;
        border-radius: 10px 10px 0 0;
    }

    .inquiry-header h1 {
        margin: 0;
        font-size: 20px;
    }

    .inquiry-card {
        background: white;
        border-radius: 0 0 10px 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .inquiry-body {
        padding: 20px;
    }

    .detail-row {
        display: flex;
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }

    .detail-label {
        font-weight: bold;
        width: 130px;
        color: #555;
    }

    .detail-value {
        flex: 1;
        color: #333;
    }

    .badge-category {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 15px;
        font-size: 11px;
        font-weight: bold;
    }

    .badge-genuine {
        background: #28a745;
        color: white;
    }

    .badge-nonserious {
        background: #ffc107;
        color: #333;
    }

    .progress-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
        border-left: 3px solid rgb(104, 75, 142);
    }

    .progress-title {
        margin: 0 0 10px 0;
        color: rgb(104, 75, 142);
        font-size: 16px;
        font-weight: bold;
    }

    .progress-status-box {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: white;
        border-radius: 6px;
    }

    .status-bullet {
        font-size: 16px;
        font-weight: bold;
    }

    .status-text {
        font-size: 14px;
        font-weight: 500;
    }

    .progress-info-box {
        background: white;
        padding: 10px;
        border-radius: 6px;
        margin-top: 8px;
        font-size: 13px;
    }

    .btn-back {
        background: rgb(104, 75, 142);
        color: white;
        padding: 8px 20px;
        border-radius: 5px;
        text-decoration: none;
        display: inline-block;
        font-size: 14px;
    }

    .btn-back:hover {
        background: #5a3d7a;
        color: white;
    }

    .text-muted {
        color: #6c757d;
        font-size: 13px;
        margin: 8px 0 0 0;
    }

    .text-center {
        text-align: center;
    }

    .mt-3 {
        margin-top: 15px;
    }
</style>

<div class="inquiry-container">
    <div class="inquiry-card">
        <div class="inquiry-header">
            <h1>📋 Inquiry Details</h1>
        </div>

        <div class="inquiry-body">
            <form action="{{ route('mcmc.update.category', $inquiry->InquiryID) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="detail-row">
                    <div class="detail-label">ID:</div>
                    <div class="detail-value">{{ $inquiry->InquiryID }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Title:</div>
                    <div class="detail-value"><strong>{{ $inquiry->InquiryTitle }}</strong></div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Description:</div>
                    <div class="detail-value">{{ $inquiry->InquiryDescription }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Category:</div>
                    <div class="detail-value">
                        <span class="badge-category {{ $inquiry->SubmissionCategory == 'Genuine' ? 'badge-genuine' : 'badge-nonserious' }}">
                            {{ $inquiry->SubmissionCategory ?? 'Not assigned' }}
                        </span>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Status:</div>
                    <div class="detail-value">{{ ucfirst($inquiry->SubmissionStatus ?? 'Pending') }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Submitted At:</div>
                    <div class="detail-value">{{ \Carbon\Carbon::parse($inquiry->SubmissionDate)->format('d M Y') }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Submission Link:</div>
                    <div class="detail-value">
                        <a href="{{ $inquiry->SubmissionLink }}" target="_blank">{{ $inquiry->SubmissionLink }}</a>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Evidence File:</div>
                    <div class="detail-value">
                        @if($inquiry->SubmissionEvidence)
                            <a href="{{ asset('storage/evidence/' . $inquiry->SubmissionEvidence) }}" target="_blank">View Evidence</a>
                        @else
                            Not Provided
                        @endif
                    </div>
                </div>

                {{-- Progress Section --}}
                <div class="progress-section">
                    <div class="progress-title">📊 Inquiry Progress</div>
                    
                    @php
                        $progress = $inquiry->latestProgress;
                        $statusText = 'Pending';
                        $statusColor = '#ffc107';
                        $bulletSymbol = '●';
                        
                        if ($progress) {
                            if ($progress->VerificationStatus === 'Verified as True') {
                                $statusText = 'Verified as True - Investigation Complete';
                                $statusColor = '#28a745';
                                $bulletSymbol = '✓';
                            } elseif ($progress->VerificationStatus === 'Identified as Fake') {
                                $statusText = 'Identified as Fake - Case Closed';
                                $statusColor = '#dc3545';
                                $bulletSymbol = '✗';
                            } elseif ($progress->VerificationStatus === 'Rejected') {
                                $statusText = 'Rejected - Insufficient Evidence';
                                $statusColor = '#dc3545';
                                $bulletSymbol = '✗';
                            } elseif ($progress->InvestigationBeginDate) {
                                $statusText = 'Under Investigation';
                                $statusColor = '#17a2b8';
                                $bulletSymbol = '●';
                            } elseif ($inquiry->latestAssignment) {
                                $statusText = 'Forwarded to Agency - Pending Review';
                                $statusColor = '#ffc107';
                                $bulletSymbol = '●';
                            }
                        } elseif ($inquiry->latestAssignment) {
                            $statusText = 'Forwarded to Agency - Pending Review';
                            $statusColor = '#ffc107';
                            $bulletSymbol = '●';
                        } else {
                            $statusText = 'Pending Assignment';
                            $statusColor = '#6c757d';
                            $bulletSymbol = '○';
                        }
                    @endphp
                    
                    <div class="progress-status-box">
                        <span class="status-bullet" style="color: {{ $statusColor }};">{{ $bulletSymbol }}</span>
                        <span class="status-text" style="color: {{ $statusColor }};">{{ $statusText }}</span>
                    </div>
                    
                    @if($progress && $progress->InvestigationDetails)
                        <div class="progress-info-box">
                            <strong>Investigation Details:</strong> {{ $progress->InvestigationDetails }}
                        </div>
                    @endif
                    
                    @if($progress && $progress->InvestigationDoc)
                        <div class="progress-info-box">
                            <strong>Supporting Document:</strong>
                            <a href="{{ route('progress.view.pdf', $progress->StatusID) }}" target="_blank">📄 View Document</a>
                        </div>
                    @endif

                    @if(!$progress && !$inquiry->latestAssignment)
                        <p class="text-muted">Not assigned to any agency yet.</p>
                    @endif
                </div>

                <div class="text-center mt-3">
                    <a href="{{ route('mcmc.all.inquiry') }}" class="btn-back">← Back to List</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
