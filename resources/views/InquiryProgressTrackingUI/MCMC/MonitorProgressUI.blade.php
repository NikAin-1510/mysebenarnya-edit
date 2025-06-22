@extends('layouts.layout')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/module4/mcmc.css') }}">
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
<div class="container">
    <h2 style="margin-bottom: 25px;">Monitor Inquiry Progress</h2>
    <p><strong>Inquiry Title:</strong> {{ $inquiry->InquiryTitle ?? 'N/A' }}</p>
    @forelse($progressList as $progress)
        @php
            $statusDisplay = $progress->VerificationStatus
            ?? ($progress->InvestigationBeginDate ? 'Under Investigation' : 'N/A');
        @endphp
        <p><strong>Status:</strong> {{ $statusDisplay }}</p>

        @if($progress->VerificationStatus === 'Rejected')
            <p><strong>Verification Date:</strong> {{ $progress->VerificationDateTime ?? 'Not available' }}</p>
        @elseif(in_array($progress->VerificationStatus, ['Verified as True', 'Identified as Fake']))
            <p><strong>Investigation Start Date:</strong> {{ $progress->InvestigationBeginDate ?? 'Not started' }}</p>
            <p><strong>Verification Date:</strong> {{ $progress->VerificationDateTime ?? 'Not available' }}</p>
        @elseif(is_null($progress->VerificationStatus) && $progress->InvestigationBeginDate)
            <p><strong>Investigation Start Date:</strong> {{ $progress->InvestigationBeginDate }}</p>
        @endif

        <p><strong>Investigation Details:</strong></p>
        <p>{{ $progress->InvestigationDetails ?? 'No details provided.' }}</p>

        <p><strong>Supporting Document:</strong>
        @if($progress->InvestigationDoc)
            <a href="{{ route('progress.view.pdf', $progress->StatusID) }}" target="_blank">📄 View / Download File</a>
        @else
            None uploaded
        @endif
        </p>


        @if($progress->Notify === 'Reassignment requested')
            <p><strong>⚠ Reassignment Requested</strong></p>
        @elseif($progress->Notify === 'Inquiry is completed')
            <p><strong>✅ {{ $progress->AgencyName }} has marked this inquiry as completed.</strong></p>
        @elseif($progress->Notify === 'Further clarification needed')
            <p><strong>🗂 Further clarification needed from {{ $progress->AgencyName }}.</strong></p>
        @endif


        @empty
            <p>No agencies have submitted progress yet for this inquiry.</p>
    @endforelse

    <div class="done-button">
        <a href="{{ route('mcmc.all.inquiry') }}">Done</a>
    </div>
    </div>
@endsection
