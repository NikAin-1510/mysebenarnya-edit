@extends('layouts.layout')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module3/ownlistinquiry.css') }}">
@endpush
@section('List Inquiry', 'Assigned Inquiries')

@section('content')
<div class="inquiries-section" id="inquiriesGrid">
  @forelse($inquiries as $inq)
    <div class="inquiry-card" data-status="{{ strtolower($inq->SubmissionStatus) }}">
      <div class="inquiry-header">
        <span class="inquiry-id">{{ $inq->InquiryID }}</span>

        {{-- Show status only if not pending --}}
        @if(strtolower($inq->SubmissionStatus) === 'forwarded')
          <span class="inquiry-status status-forwarded">Forwarded to Agency</span>
        @elseif(strtolower($inq->SubmissionStatus) === 'completed')
          <span class="inquiry-status status-completed">Verification Complete</span>
        @endif
      </div>

      <div class="inquiry-title">{{ $inq->InquiryTitle }}</div>
      <div class="inquiry-description">{{ $inq->InquiryDescription }}</div>

      <div class="inquiry-dates">
        <div class="date-item">
          <i class="fas fa-calendar-plus"></i>
          Submitted: {{ date('d M Y', strtotime($inq->SubmissionDate)) }}
        </div>
        @if($inq->AssignDate)
          <div class="date-item">
            <i class="fas fa-share-square"></i>
            Forwarded: {{ date('d M Y', strtotime($inq->AssignDate)) }}
          </div>
        @endif
        @if($inq->VerificationDateTime)
          <div class="date-item">
            <i class="fas fa-check-circle"></i>
            Completed: {{ date('d M Y', strtotime($inq->VerificationDateTime)) }}
          </div>
        @endif
      </div>

      <div class="inquiry-actions">
        <a href="{{ route('details.own.inquiry', $inq->InquiryID) }}" class="view-details-btn">View Details</a>
      </div>
    </div>
  @empty
    <div class="empty-state">
      <i class="fas fa-inbox"></i>
      <p>No inquiries submitted yet.</p>
    </div>
  @endforelse
</div>
@endsection
