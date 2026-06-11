@extends('layouts.layout')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module2/mcmc-new-details.css') }}">
@endpush

@section('content')
<div class="container">
    <h1>Inquiry Details</h1>

    <div class="inquiry-details-card">
        <span class="inquiry-id-badge">ID: {{ $inquiry->InquiryID }}</span>
        <p><strong>Title:</strong> {{ $inquiry->InquiryTitle }}</p>
        <p><strong>Description:</strong> {{ $inquiry->InquiryDescription }}</p>

        <p><strong>Category:</strong> {{ $inquiry->SubmissionCategory }}</p>
        <p><strong>Status:</strong> {{ ucfirst($inquiry->SubmissionStatus) }}</p>
        <p><strong>Submitted At:</strong> {{ \Carbon\Carbon::parse($inquiry->SubmissionDate)->format('d M Y') }}</p>

        {{-- Evidence Section --}}
        <p><strong>Submission Link:</strong>
            <a href="{{ $inquiry->SubmissionLink }}" target="_blank">{{ $inquiry->SubmissionLink }}</a>
        </p>

        @if($inquiry->SubmissionEvidence)
            <p><strong>Evidence File:</strong>
                <a href="{{ asset('storage/evidence/' . $inquiry->SubmissionEvidence) }}" target="_blank">View Evidence</a>
            </p>
        @else
            <p><strong>Evidence File:</strong> Not Provided</p>
        @endif

        <p><strong>Agency:</strong>
            @if($inquiry->latestAssignment && $inquiry->latestAssignment->agency)
                {{ $inquiry->latestAssignment->agency->AgencyName }}
            @else
                Unassigned
            @endif
        </p>
    </div>

    <div class="text-center mt-4">
        <br>

        <a href="{{ route('mcmc.assign.form', $inquiry->InquiryID) }}"
        class="btn-next">
           Assign Agency
        </a>

        <a href="{{ route('mcmc.all.inquiry') }}"
        class="btn-back">
            Back to List
        </a>
    </div>
</div>
@endsection
