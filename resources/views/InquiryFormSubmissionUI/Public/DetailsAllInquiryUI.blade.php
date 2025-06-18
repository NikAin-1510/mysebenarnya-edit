@extends('layouts.layout')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module2/details-own-inquiry.css') }}">
@endpush

@section('page-name', 'Inquiry Details')

@section('content')
<div class="container">
    <h1>Inquiry Details</h1>

    <div class="inquiry-info">
        <p><strong>Inquiry ID:</strong> {{ $inquiry->InquiryID }}</p>
        <p><strong>Title:</strong> {{ $inquiry->InquiryTitle }}</p>
        <p><strong>Description:</strong> {{ $inquiry->InquiryDescription }}</p>
        <p><strong>Link Source:</strong>
            <a href="{{ $inquiry->LinkSource }}" target="_blank">{{ $inquiry->LinkSource }}</a>
        </p>
        @if($inquiry->Evidence)
        <p><strong>Evidence:</strong>
            <a href="{{ asset('evidence/' . $inquiry->Evidence) }}" target="_blank">View File</a>
        </p>
        @endif
        <p><strong>Category:</strong> {{ $inquiry->SubmissionCategory }}</p>
    </div>

    <div class="button-group" style="margin-top: 20px;">
        <a href="{{ route('inquiry.assigned.agency', $inquiry->InquiryID) }}" class="btn btn-primary">Next</a>
    </div>
</div>
