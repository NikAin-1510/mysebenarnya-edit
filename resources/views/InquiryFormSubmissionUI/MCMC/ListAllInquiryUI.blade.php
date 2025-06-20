
@extends('layouts.layout')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module2/all-inquiry.css') }}">
@endpush
<section class="content">
  <div class="dashboard-header">
    <h1><i class="fas fa-history"></i> Inquiry Record History</h1>
    <p>View, filter, and manage previously submitted public inquiries</p>
  </div>

  <form method="GET" action="{{ route('mcmc.all.inquiry') }}" class="filter-form">
    <input type="date" name="start_date" value="{{ request('start_date') }}">
    <input type="date" name="end_date" value="{{ request('end_date') }}">
    <select name="status">
      <option value="">All Status</option>
      <option value="Genuine" {{ request('status') == 'Genuine' ? 'selected' : '' }}>Genuine</option>
      <option value="NonSerious" {{ request('status') == 'NonSerious' ? 'selected' : '' }}>Non-Serious</option>
    </select>
    <select name="agency">
      <option value="">All Agencies</option>
      @foreach($agencies as $agency)
        <option value="{{ $agency->id }}" {{ request('agency') == $agency->id ? 'selected' : '' }}>{{ $agency->name }}</option>
      @endforeach
    </select>
    <button type="submit"><i class="fas fa-search"></i> Filter</button>
  </form>

  <div class="inquiries-section">
    @foreach($inquiries as $inq)
      <div class="inquiry-card">
        <div class="inquiry-header">
          <span class="inquiry-id">{{ $inq->InquiryID }}</span>
          <span class="inquiry-status badge-{{ strtolower($inq->SubmissionCategory) }}">
            {{ $inq->SubmissionCategory ?? 'Uncategorized' }}
          </span>
        </div>
        <div class="inquiry-title">{{ $inq->InquiryTitle }}</div>
        <div class="inquiry-description">{{ $inq->InquiryDescription }}</div>
        <div class="inquiry-dates">
          <div><i class="fas fa-calendar-plus"></i> Submitted: {{ date('d M Y', strtotime($inq->SubmissionDate)) }}</div>
          @if($inq->AssignDate)
            <div><i class="fas fa-share"></i> Assigned: {{ date('d M Y', strtotime($inq->AssignDate)) }}</div>
          @endif
          @if($inq->VerificationDateTime)
            <div><i class="fas fa-check-circle"></i> Completed: {{ date('d M Y', strtotime($inq->VerificationDateTime)) }}</div>
          @endif
        </div>
        <div class="inquiry-footer">
          <div><i class="fas fa-building"></i> {{ $inq->AgencyName ?? 'Unassigned' }}</div>
          <a href="{{ route('mcmc.all.details', $inq->InquiryID) }}" class="btn-view">View Details</a>
        </div>
      </div>
    @endforeach
  </div>
</section>
</body>
</html>
