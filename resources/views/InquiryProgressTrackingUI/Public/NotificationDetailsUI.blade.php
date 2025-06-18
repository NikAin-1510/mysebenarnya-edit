@extends('layouts.layout')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/module4/agency-inquiry.css') }}">
@endsection

@section('content')
    <div class="notification-list">
        <h2>Notification: Inquiry Status Update</h2>

        <p><strong>Inquiry Title:</strong> {{ $inquiry->InquiryTitle }}</p>

        @if ($latestProgress)

            @if ($latestProgress->VerificationStatus === 'Rejected')
                {{-- Minimal output for Rejected --}}
                <p><strong>Status:</strong> Rejected</p>
                <p>Your inquiry <strong>{{ $inquiry->InquiryTitle }}</strong> was <strong>rejected</strong>.</p>
                <p>Please check your dashboard for more details.</p>

                <h3>Log Progress:</h3>
                <p><strong>Investigation Begin Date:</strong> -</p>
                <p><strong>Verification Status:</strong> Rejected</p>
                <p><strong>Verification Date:</strong>
                    {{ $latestProgress->VerificationDateTime
                        ? \Carbon\Carbon::parse($latestProgress->VerificationDateTime)->format('Y-m-d H:i:s')
                        : '-' }}
                </p>

            @elseif (request()->query('status') === 'Under Investigation')
                {{-- Full output for Under Investigation --}}
                <p><strong>Latest Status:</strong> Under Investigation</p>
                <p>Your inquiry <strong>{{ $inquiry->InquiryTitle }}</strong> is currently <strong>Under investigation</strong>.</p>

                <h3>Log Progress:</h3>
                <p><strong>Investigation Begin Date:</strong>
                    {{ $latestProgress->InvestigationBeginDate
                        ? \Carbon\Carbon::parse($latestProgress->InvestigationBeginDate)->format('Y-m-d H:i:s')
                        : '-' }}
                </p>
                <p><strong>Verification Status:</strong> Under Investigation</p>
                <p><strong>Verification Date:</strong> -</p>

            @else
                {{-- Full output for other verification results --}}
                <p><strong>Latest Status:</strong> {{ $latestProgress->VerificationStatus }}</p>
                <p>Your inquiry <strong>{{ $inquiry->InquiryTitle }}</strong> was
                    <strong>{{ $latestProgress->VerificationStatus }}</strong>.
                </p>

                <h3>Log Progress:</h3>
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
            @endif

        @else
            <p><strong>Status update:</strong> No progress record found for this status.</p>
        @endif

        <p>Please check your dashboard for more details.</p>
    </div>
@endsection
