@extends('layouts.layout')

@section('page-name', 'Track Cases')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module3/agency.css') }}">
@endpush

@section('content')
    <h2><i class="fas fa-tasks"></i> Track Cases - Assigned Inquiries</h2>
    <p>Review and manage inquiries assigned by MCMC for verification</p>

    <div class="inquiry-grid">
        @foreach($assignments as $assign)
            <div class="inquiry-card" onclick="window.location='{{ route('agency.inquiry.details', $assign->AssignmentID) }}'">
                <div class="inquiry-card-header">
                    <h4>{{ $assign->inquiry->InquiryTitle }}</h4>
                    <div class="inquiry-card-meta">
                        <span><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($assign->AssignDate)->format('Y-m-d') }}</span>
                        <span><i class="fas fa-user"></i> {{ $assign->AgencyName ?? '-' }}</span>
                    </div>
                </div>
                <div class="inquiry-card-body">
                    <p><strong>ID:</strong> {{ $assign->InquiryID }}</p>
                    <p><strong>Status:</strong>
                        <span class="inquiry-status status-{{ strtolower($assign->progress->VerificationStatus ?? 'pending') }}">
                            {{ strtoupper($assign->progress->VerificationStatus ?? 'PENDING') }}
                        </span>
                    </p>
                </div>
            </div>
        @endforeach
    </div>
@endsection


// resources/views/InquiryFormSubmissionUI/Agency/InquiryDetailsUI.blade.php

@extends('layouts.layout')

@section('page-name', 'Inquiry Details')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/agency.css') }}">
@endpush

@section('content')
    <div class="inquiry-details">
        <div class="inquiry-header">
            <h2>{{ $assignment->inquiry->InquiryTitle }}</h2>
            <div class="inquiry-meta">
                <span><i class="fas fa-calendar"></i> {{ $assignment->AssignDate }}</span>
                <span><i class="fas fa-user"></i> {{ $assignment->AgencyName ?? '-' }}</span>
                <span><i class="fas fa-tag"></i> {{ $assignment->inquiry->SubmissionStatus }}</span>
            </div>
        </div>

        <div class="inquiry-body">
            <a href="{{ route('agency.inquiries') }}" class="btn btn-cancel" style="margin-bottom: 20px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>

            <div class="section">
                <h3><i class="fas fa-info-circle"></i> Inquiry Details</h3>
                <div class="detail-grid">
                    <div class="detail-item"><label>Inquiry ID:</label><span>{{ $assignment->InquiryID }}</span></div>
                    <div class="detail-item"><label>Status:</label><span>{{ $assignment->progress->VerificationStatus ?? '-' }}</span></div>
                    <div class="detail-item"><label>Description:</label><span>{{ $assignment->inquiry->InquiryDescription }}</span></div>
                    <div class="detail-item"><label>Link:</label><span>{{ $assignment->inquiry->SubmissionLink }}</span></div>
                </div>
            </div>

            <div class="section">
                <h3><i class="fas fa-history"></i> Investigation Timeline</h3>
                <div class="history-timeline">
                    @if ($assignment->progress)
                        <div class="history-item">
                            <div class="history-date">{{ $assignment->progress->InvestigationBeginDate }}</div>
                            <div class="history-action">{{ $assignment->progress->InvestigationDetails }}</div>
                        </div>
                    @else
                        <p>No progress recorded yet.</p>
                    @endif
                </div>
            </div>

            <div class="jurisdiction-review">
                <h3><i class="fas fa-gavel"></i> Jurisdiction Review</h3>
                <p>Does this inquiry fall under your agency's scope? Accept if yes, or reject with reason if no.</p>

                <form method="POST" action="{{ route('agency.inquiry.action', $assignment->AssignmentID) }}">
                    @csrf
                    <div class="action-buttons">
                        <button type="submit" name="action" value="accept" class="btn btn-accept">
                            <i class="fas fa-check"></i> Accept & Proceed
                        </button>
                        <button type="button" class="btn btn-reject" onclick="document.getElementById('rejectModal').style.display='block'">
                            <i class="fas fa-times"></i> Reject Inquiry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Reject Inquiry</h3>
                <span class="close" onclick="document.getElementById('rejectModal').style.display='none'">&times;</span>
            </div>
            <form method="POST" action="{{ route('agency.inquiry.action', $assignment->AssignmentID) }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejectReason">Reason for Rejection:</label>
                        <textarea name="rejectReason" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" onclick="document.getElementById('rejectModal').style.display='none'">Cancel</button>
                    <button type="submit" name="action" value="reject" class="btn btn-submit">Submit Rejection</button>
                </div>
            </form>
        </div>
    </div>
@endsection
