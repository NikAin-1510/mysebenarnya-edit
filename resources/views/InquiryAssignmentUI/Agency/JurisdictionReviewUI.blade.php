@extends('layouts.layout')
@section('page-name', 'Inquiry Details')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module3/jurisdiction.css') }}">
@endpush
@section('content')
<div class="container">
    <h1>Inquiry Details</h1>

    <div class="inquiry-details-card">
        <span class="inquiry-id-badge">ID: {{ $assignment->InquiryID }}</span>
        <p><strong>Title:</strong> {{ $assignment->inquiry->InquiryTitle ?? '-' }}</p>
        <p><strong>Description:</strong> {{ $assignment->inquiry->InquiryDescription ?? '-' }}</p>
        <p><strong>Submission Link:</strong>
            <a href="{{ $assignment->inquiry->SubmissionLink ?? '#' }}" target="_blank">
                {{ $assignment->inquiry->SubmissionLink ?? '-' }}
            </a>
        </p>
        <p><strong>Assigned Date:</strong> {{ $assignment->AssignDate }}</p>
        <p><strong>Current Status:</strong> {{ $assignment->progress->VerificationStatus ?? 'Pending' }}</p>

        <!-- Jurisdiction Review Section  -->
        <hr style="margin: 20px 0; border: 1px solid #e9ecef;">
        <h3 style="color: #333; margin-bottom: 15px; font-size: 20px;"><i class="fas fa-gavel"></i> Jurisdiction Review</h3>
        <p style="color: #666; margin-bottom: 20px;">Does this inquiry fall under your agency's scope? Accept if yes, or reject with reason if no.</p>

        <form method="POST" action="{{ route('agency.inquiry.action', $assignment->AssignmentID) }}">
            @csrf
            <div class="action-buttons">
                <button type="submit" name="action" value="accept" class="btn-save">
                    <i class="fas fa-check"></i> Accept
                </button>
                <button type="button" class="btn-next" onclick="showRejectForm()">
                    <i class="fas fa-times"></i> Reject Inquiry
                </button>
            </div>
        </form>

        <!-- Reject Form Section - now inside the main card -->
        <div class="reject-form-section" id="rejectForm">
            <h4><i class="fas fa-times-circle"></i> Reject Inquiry</h4>
            <form method="POST" action="{{ route('agency.inquiry.action', $assignment->AssignmentID) }}">
                @csrf
                <div class="form-group">
                    <label for="jurisdictionComment">Reason for Rejection:</label>
                    <textarea name="jurisdictionComment" id="jurisdictionComment" required placeholder="Please provide a detailed reason for rejecting this inquiry..."></textarea>
                </div>
                <div class="reject-form-buttons">
                    <button type="button" class="btn-back" onclick="hideRejectForm()">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </button>
                    <button type="submit" name="action" value="reject" class="btn-save">
                        <i class="fas fa-paper-plane"></i> Submit Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="{{ route('agency.inquirylist') }}" class="btn-back">Back to List</a>
    </div>
</div>

<script>
function showRejectForm() {
    document.getElementById('rejectForm').classList.add('show');
    document.getElementById('jurisdictionComment').focus();
}
function hideRejectForm() {
    document.getElementById('rejectForm').classList.remove('show');
    document.getElementById('jurisdictionComment').value = '';
}
</script>
@endsection
