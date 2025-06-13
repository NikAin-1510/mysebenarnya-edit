@extends('layouts.layout')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module3/mcmc-dashboard.css') }}">
@endpush
    <div class="sidebar">
        <ul>
            <li><a href="{{ route('agency.review.inquiry') }}" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="{{ route('agency.assign.form') }}"><i class="fas fa-tasks"></i> Assign Inquiries</a></li>
            <li><a href="{{ route('agency.display.report') }}"><i class="fas fa-chart-line"></i> Generate Reports</a></li>
        </ul>
    </div>

    <section class="content">
        <div class="content-header">
            <h1>MCMC Dashboard</h1>
            <p>Manage inquiry assignments and generate reports</p>
        </div>

        <div class="dashboard-cards">
            <div class="card">
                <h3><i class="fas fa-inbox"></i> Pending Inquiries</h3>
                <div style="font-size: 36px; font-weight: bold; color: rgb(104, 75, 142);">
                   {{ $pendingInquiries }}
                </div>
                <p>Inquiries waiting for assignment</p>
            </div>

            <div class="card">
                <h3><i class="fas fa-check-circle"></i> Assigned Today</h3>
                <div style="font-size: 36px; font-weight: bold; color: #28a745;">
                    {{ $assignments->filter(fn($a) => \Carbon\Carbon::parse($a->AssignDate)->isToday())->count() }}
                </div>
                <p>Inquiries assigned to agencies today</p>
            </div>

        <div class="card">
            <h3><i class="fas fa-clock"></i> Recent Assignment History</h3>
            <div class="assignment-history">
                @foreach($assignments as $a)
                    <div class="assignment-item">
                        <div class="agency-name">{{ $a->agency->AgencyName ?? 'N/A' }}</div>
                        <div class="date">Assigned on: {{ \Carbon\Carbon::parse($a->AssignDate)->format('F d, Y') }}</div>
                        <div class="inquiry-title">{{ $a->inquiry->InquiryTitle ?? 'N/A' }}</div>
                        @if($a->InquiryComment)
                            <div style="color: #666; font-size: 14px; margin-top: 5px;"><strong>Notes:</strong> {{ $a->InquiryComment }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</body>
</html>
