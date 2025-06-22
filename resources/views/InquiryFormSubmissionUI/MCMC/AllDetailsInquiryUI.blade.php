@extends('layouts.layout')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module2/mcmc-new-details.css') }}">
@endpush

@section('content')
<div class="container">
    <h1>Inquiry Details</h1>

    <form action="{{ route('mcmc.update.category', $inquiry->InquiryID) }}" method="POST">
        @csrf
        @method('PUT')

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

        </div>

        <div class="text-center mt-4">
            <br>
            <a href="{{ route('mcmc.all.inquiry') }}" class="btn-back">Back to List</a>

            {{-- Next button conditionally shown --}}
            <a href="{{ route('mcmc.review.inquiry', $inquiry->InquiryID) }}" class="btn-next" id="next-button">Next</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endpush
