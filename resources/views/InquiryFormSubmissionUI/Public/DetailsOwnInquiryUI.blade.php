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
</div>

<div class="text-center mt-4">
    <br>
    @if(in_array(strtolower($inquiry->SubmissionStatus), ['completed', 'verified']))
        <a href="{{ route('assigned.agency.view', $inquiry->InquiryID) }}" class="btn-next">View Assigned Agency</a>
    @else
        <a href="{{ route('public.list') }}" class="btn-back">Back to List</a>
    @endif
</div>



</div>
@endsection
