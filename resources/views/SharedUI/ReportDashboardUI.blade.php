@extends('layout')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/reportdashboard.css') }}">
@endpush

@section('page-name', 'Report Dashboard')

@section('content')
<section class="content">
  <div class="report-container">
    <h2>Select Report</h2>
    <div class="report-selection-grid">
      <button onclick="location.href='{{ route('show.registeredUserReport') }}'">Registered User Report</button>
      <button onclick="location.href='{{ route('show.totalInquiryReport') }}'">Total Public Inquiries Report</button>
      <button onclick="location.href='{{ route('show.inquiryAssignedReport') }}'">Inquiries Assigned per Agency</button>
      <button onclick="location.href='{{ route('show.agencyPerformanceReport') }}'">Agency Performance Report</button>
    </div>
  </div>
</section>
@endsection
