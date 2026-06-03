@extends('layouts.layout')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/module4/public-details.css') }}">
@endsection

@section('page-name', 'Notification Details')

@section('content')
    <div class="container">
        <h2>Notification: Inquiry Status Update</h2>
            <p class="title"><strong>Inquiry Title:</strong> {{ $inquiry->InquiryTitle }}</p>

        @if ($latestProgress)
            @if ($latestProgress->VerificationStatus === 'Rejected')
                {{-- Minimal output for Rejected --}}
                <p style="margin-left: 20px">Your inquiry <strong>{{ $inquiry->InquiryTitle }}</strong> was <strong>rejected</strong>.</p>
                <div class="progress-card">
                    <h3>Status Update</h3>
                    <p><strong>Status:</strong> Rejected</p>
                    <div class="text">
                    </div>
                </div>

                <div class="progress-card">
                    <h3>Log Progress</h3>
                    <p><strong>Investigation Begin Date:</strong> -</p>
                    <p><strong>Verification Status:</strong> Rejected</p>
                    <p><strong>Verification Date:</strong>
                        {{ $latestProgress->VerificationDateTime
                            ? \Carbon\Carbon::parse($latestProgress->VerificationDateTime)->format('Y-m-d H:i:s')
                            : '-' }}
                    </p>
                </div>

            @elseif (request()->query('status') === 'Under Investigation')
                {{-- Full output for Under Investigation --}}
                <p style="margin-left: 20px">Your inquiry <strong>{{ $inquiry->InquiryTitle }}</strong> is currently <strong>Under investigation</strong>.</p>
                <div class="progress-card">
                    <h3>Status Update</h3>
                    <p><strong>Latest Status:</strong> Under Investigation</p>
                </div>

                <div class="progress-card">
                    <h3>Log Progress</h3>
                    <p><strong>Investigation Begin Date:</strong>
                        {{ $latestProgress->InvestigationBeginDate
                            ? \Carbon\Carbon::parse($latestProgress->InvestigationBeginDate)->format('Y-m-d H:i:s')
                            : '-' }}
                    </p>
                    <p><strong>Verification Status:</strong> Under Investigation</p>
                    <p><strong>Verification Date:</strong> -</p>
                </div>

            @else
                {{-- Full output for other verification results --}}
                <p style="margin-left: 20px">Your inquiry <strong>{{ $inquiry->InquiryTitle }}</strong> was
                <strong>{{ $latestProgress->VerificationStatus }}</strong>.
                <div class="progress-card">
                    <h3>Status Update</h3>
                    <p><strong>Latest Status:</strong> {{ $latestProgress->VerificationStatus }}</p>
                    </p>
                </div>

                <div class="progress-card">
                    <h3>Log Progress</h3>
                    <p><strong>Investigation Begin Date:</strong>
                        {{ $latestProgress->InvestigationBeginDate
                            ? \Carbon\Carbon::parse($latestProgress->InvestigationBeginDate)->format('Y-m-d H:i:s')
                            : '-' }}
                    </p>
                    <p><strong>Verification Status:</strong> {{ $latestProgress->VerificationStatus }}</p>
                    <p><strong>Verification Date:</strong>
                        {{ $latestProgress->VerificationDateTime
                            ? \Carbon\Carbon::parse($latestProgress->VerificationDateTime)->format('Y-m-d H:i:s')
                            : '-' }}
                    </p>
                </div>
            @endif

        @else
            <div class="progress-card">
                <p><strong>Status update:</strong> No progress record found for this status.</p>
            </div>
        @endif

        <p>Please check inquiry list for more details.</p>


    <div class="done-button">
        <a href="{{ route('notification.list') }}">Done</a>
    </div>
    </div>
@endsection
