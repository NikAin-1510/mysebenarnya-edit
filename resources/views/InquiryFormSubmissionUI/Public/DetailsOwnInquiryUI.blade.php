@extends('layouts.layout')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module2/details-own-inquiry.css') }}">
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
     @if($assignedAgency && $assignedAgency->AgencyID && $assignedAgency->agency)
    <p><strong>Agency Name:</strong> {{ $assignedAgency->agency->AgencyName }}</p>
@else
    <p><strong>Agency Name:</strong> <span class="text-muted">Unassigned</span></p>
@endif



 <div class="text-center mt-4">
        <br>
       <a href="{{ route('public.list') }}" class="btn-back">Back to List</a>

@if($assignedAgency && $assignedAgency->AgencyID && $assignedAgency->agency)
    <a href="{{ url('/public/own-inquiry-details?id=' . $inquiry->InquiryID) }}" class="btn-next">Next</a>
@endif


    </div>
</div>
@endsection

    </div>

