@extends('layouts.layout')
@section('page-name', 'Inquiry Progress')
@section('head')
    <link rel="stylesheet" href="{{ asset('css/module4/public-details.css') }}">
@endsection

@section('content')
<div class="container">
    <h2 style="margin-bottom: 25px;">Inquiry Progress</h2>

    @forelse($progressList as $progress)
        <div class="progress-card">
            <p><strong>Inquiry Title:</strong> {{ $inquiry->InquiryTitle ?? 'N/A' }}</p>
            <p><strong>Status:</strong> {{ $progress->VerificationStatus ?? 'N/A' }}</p>

            @if($progress->VerificationStatus === 'Rejected')
                <p>The inquiry you submitted is dismissed due to irrelevance or lack of sufficient evidence.</p>
                <p><strong>Investigation Start Date:</strong> No investigation conducted</p>
                <p><strong>Verification Date:</strong> {{ $progress->VerificationDateTime ?? 'Not available' }}</p>
            @elseif(in_array($progress->VerificationStatus, ['Verified as True', 'Identified as Fake']))
                <p><strong>Investigation Start Date:</strong> {{ $progress->InvestigationBeginDate ?? 'Not started' }}</p>
                <p><strong>Verification Date:</strong> {{ $progress->VerificationDateTime ?? 'Not available' }}</p>
            @elseif($progress->VerificationStatus === 'Under Investigation')
                <p><strong>Investigation Start Date:</strong> {{ $progress->InvestigationBeginDate ?? 'Not started' }}</p>
            @endif
            <p><strong>Investigation Details:</strong> {{ $progress->InvestigationDetails ?? 'No details provided.' }}</p>
        </div>
    @empty
        <p>No progress found for this inquiry.</p>
    @endforelse

    <div class="done-button">
        <a href="{{ route('public.list') }}">Done</a>
    </div>
</div>
@endsection
