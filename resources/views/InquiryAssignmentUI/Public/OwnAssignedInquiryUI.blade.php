@extends('layouts.layout')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module3/public-dashboard.css') }}">
@endpush
@section('List Inquiry', 'Assigned Inquiries') {{-- Optional: Changes header title --}}

@section('content')
<div class="inquiries-section" id="inquiriesGrid">
  @foreach($inquiries as $inq)
    <div class="inquiry-card" data-status="{{ strtolower($inq->SubmissionStatus) }}">
  <div class="inquiry-header">
    <span class="inquiry-id">{{ $inq->InquiryID }}</span>
    <span class="inquiry-status status-{{ strtolower($inq->SubmissionStatus) }}">
      @if($inq->SubmissionStatus == 'forwarded') Forwarded to Agency
      @elseif($inq->SubmissionStatus == 'completed') Verification Complete
      @else Pending Assignment
      @endif
    </span>
  </div>
  <div class="inquiry-title">{{ $inq->InquiryTitle }}</div>
  <div class="inquiry-description">{{ $inq->InquiryDescription }}</div>
  <div class="agency-info">
    <div class="agency-header">
      <div class="agency-icon"><i class="fas fa-building"></i></div>
      <div class="agency-name">
    {{ $inq->latestAssignment?->agency?->AgencyName ?? 'Awaiting Agency Assignment' }}
</div>
    <div class="agency-details">
      @if($inq->InvestigationDetails)
        <strong>Verification Result:</strong> {{ $inq->InvestigationDetails }}
      @elseif($inq->AgencyName)
        Your inquiry has been forwarded to {{ $inq->AgencyName }}.
      @else
        Awaiting MCMC review for forwarding.
      @endif
    </div>
  </div>
  <div class="inquiry-dates">
    <div class="date-item"><i class="fas fa-calendar-plus"></i> Submitted: {{ date('d M Y', strtotime($inq->SubmissionDate)) }}</div>
    @if($inq->AssignDate)
      <div class="date-item"><i class="fas fa-share-square"></i> Forwarded: {{ date('d M Y', strtotime($inq->AssignDate)) }}</div>
    @endif
    @if($inq->VerificationDateTime)
      <div class="date-item"><i class="fas fa-check-circle"></i> Completed: {{ date('d M Y', strtotime($inq->VerificationDateTime)) }}</div>
    @endif
  </div>

  <!-- View Details Button -->


<div class="inquiry-actions">
    <a href="{{ route('details.own.inquiry', $inq->InquiryID) }}" class="view-details-btn">View Details</a>
      </div>
    </div>
  @endforeach
</div>
@endsection
