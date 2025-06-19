@extends('layouts.layout')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/module4/agency-inquiry.css') }}">
    <style>
        .progress-card {
            border: 1px solid #ccc;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .progress-card h3 {
            margin-top: 0;
        }
    </style>
@endsection

@section('content')
    <h2 style="margin-bottom: 25px;">Inquiry Progress</h2>

    <p><strong>Inquiry Title:</strong> {{ $inquiry->InquiryTitle ?? 'N/A' }}</p>

    @forelse($progressList as $progress)
        <div class="progress-card">
            <p><strong>Status:</strong> {{ $progress->VerificationStatus ?? 'N/A' }}</p>

            @if($progress->VerificationStatus === 'Rejected')
            <p>The inquiry you submitted is dismissed due to irrelevance or lack of sufficient
evidence.</p>
<p><strong>Investigation Start Date:</strong> No investigation conducted</p>
                <p><strong>Verification Date:</strong> {{ $progress->VerificationDateTime ?? 'Not available' }}</p>
            @elseif(in_array($progress->VerificationStatus, ['Verified as True', 'Identified as Fake']))
                <p><strong>Investigation Start Date:</strong> {{ $progress->InvestigationBeginDate ?? 'Not started' }}</p>
                <p><strong>Verification Date:</strong> {{ $progress->VerificationDateTime ?? 'Not available' }}</p>
            @elseif($progress->VerificationStatus === 'Under Investigation')
                <p><strong>Investigation Start Date:</strong> {{ $progress->InvestigationBeginDate ?? 'Not started' }}</p>
            @endif

            <p><strong>Investigation Details:</strong></p>
            <p>{{ $progress->InvestigationDetails ?? 'No details provided.' }}</p>
        </div>
    @empty
        <p>No progress found for this inquiry.</p>
    @endforelse
@endsection
