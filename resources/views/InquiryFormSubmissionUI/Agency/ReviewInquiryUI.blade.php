@extends('layouts.layout')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module2/review-inquiry.css') }}">
@endpush

@section('content')
<div class="container">
    <h1>Inquiry Tracking History</h1>
    <p>Below is a detailed timeline of this inquiry’s journey and all related actions.</p>

    <div class="inquiry-summary">
        <h2>{{ $inquiry->InquiryTitle }}</h2>
        <p><strong>Submitted:</strong> {{ date('d M Y, h:i A', strtotime($inquiry->SubmissionDate)) }}</p>
        <p><strong>Status:</strong> {{ $inquiry->InvestigationStatus ?? 'Under Review' }}</p>
        <p><strong>Category:</strong> {{ $inquiry->SubmissionCategory }}</p>
    </div>

    <div class="timeline">
        @foreach($historyLogs as $log)
        <div class="timeline-item">
            <div class="timeline-icon"><i class="fas fa-history"></i></div>
            <div class="timeline-content">
                <span class="timestamp">{{ date('d M Y, h:i A', strtotime($log->created_at)) }}</span>
                <p><strong>{{ $log->action_by }}</strong> - {{ $log->action }}</p>
                @if ($log->notes)
                    <p class="note">{{ $log->notes }}</p>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
