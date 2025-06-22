@extends('layouts.layout')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/reportdashboard.css') }}">
<style>
    .report-container {
        max-width: 900px;
        margin-left:5px;
        padding: 2rem;
        text-align: center;
    }

    .report-container h2 {
        font-size: 2rem;
        margin-bottom: 1.5rem;
        color: #333;
    }

    .report-selection-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.5rem;
    }

    .report-card {
        background: #f9f9f9;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s ease;
        cursor: pointer;
    }

    .report-card:hover {
        transform: translateY(-5px);
        background-color: #eef6ff;
    }

    .report-card i {
        font-size: 2rem;
        margin-bottom: 0.75rem;
        color: #7b328c;
    }

    .report-card h3 {
        font-size: 1.1rem;
        color: #222;
        margin: 0;
    }
</style>
<!-- FontAwesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('page-name', 'Report Dashboard')

@section('content')
<section class="content">
    <div class="report-container">
        <h2>Select a Report to View</h2>
        <div class="report-selection-grid">
            <div class="report-card" onclick="location.href='#'">
                <i class="fas fa-users"></i>
                <h3>Registered User Report</h3>
            </div>
            <div class="report-card" onclick="location.href='{{ route('show.totalInquiryReport')}}'">
                <i class="fas fa-question-circle"></i>
                <h3>Total Inquiries Report</h3>
            </div>
            <div class="report-card" onclick="location.href='{{ route('mcmc.report')}}'">
                <i class="fas fa-share-square"></i>
                <h3>Inquiries Assigned per Agency</h3>
            </div>
            <div class="report-card" onclick="location.href='{{ route('show.agencyPerformanceReport')}}'">
                <i class="fas fa-chart-line"></i>
                <h3>Agency Performance Report</h3>
            </div>
        </div>
    </div>
</section>
@endsection
