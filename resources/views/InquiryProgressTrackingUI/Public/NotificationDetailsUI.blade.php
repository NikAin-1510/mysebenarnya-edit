@extends('layouts.layout')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/module4/agency-inquiry.css') }}">
@endsection

@section('content')
    <div class="notification-list">
        <h2>Notification: Inquiry Status Update</h2>

        <p><strong>Inquiry Title:</strong> {{ $inquiry->InquiryTitle }}</p>

        <p><strong>Latest Status:</strong>
            @if ($latestProgress->VerificationStatus)
                {{ $latestProgress->VerificationStatus }}
            @elseif ($latestProgress->InvestigationBeginDate)
                Under Investigation Started
            @else
                N/A
            @endif
        </p>

        <p>Your inquiry <strong>{{ $inquiry->InquiryTitle }}</strong> is
            <strong>
                @if ($latestProgress->VerificationStatus)
                    {{ $latestProgress->VerificationStatus }}
                @elseif ($latestProgress->InvestigationBeginDate)
                    Under Investigation
                @else
                    not yet processed
                @endif
            </strong>.
        </p>
        <p>Please check your dashboard for more details.</p>

        <br>
        <h3>Log Progress:</h3>

        <p><strong>Current Status:</strong>
            @if ($latestProgress->VerificationStatus)
                {{ $latestProgress->VerificationStatus }}
            @elseif ($latestProgress->InvestigationBeginDate)
                Under Investigation
            @else
                Not started
            @endif
        </p>

        <p><strong>Investigation Begin Date:</strong>
            {{ $latestProgress->InvestigationBeginDate
                ? \Carbon\Carbon::parse($latestProgress->InvestigationBeginDate)->format('Y-m-d H:i:s')
                : '-' }}
        </p>

        <p><strong>Verification Date:</strong>
            {{ $latestProgress->VerificationDateTime
                ? \Carbon\Carbon::parse($latestProgress->VerificationDateTime)->format('Y-m-d H:i:s')
                : '-' }}
        </p>
    </div>
@endsection
