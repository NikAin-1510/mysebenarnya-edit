@extends('layouts.layout')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module2/details-own-inquiry.css') }}">
@endpush

@section('content')
<div class="container">
    <h1>Inquiry Details</h1>

    <form action="{{ route('mcmc.new.inquiry', $inquiry->InquiryID) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="inquiry-details-card">
            <span class="inquiry-id-badge">ID: {{ $inquiry->InquiryID }}</span>
            <p><strong>Title:</strong> {{ $inquiry->InquiryTitle }}</p>
            <p><strong>Description:</strong> {{ $inquiry->InquiryDescription }}</p>

            {{-- Dropdown for SubmissionCategory --}}
            <p><strong>Category:</strong>
                <select name="SubmissionCategory" class="form-control" required>
                    <option value="Genuine" {{ $inquiry->SubmissionCategory == 'Genuine' ? 'selected' : '' }}>Genuine</option>
                    <option value="Non-Serious" {{ $inquiry->SubmissionCategory == 'Non-Serious' ? 'selected' : '' }}>Non-Serious</option>
                </select>
            </p>

            <p><strong>Status:</strong> {{ ucfirst($inquiry->SubmissionStatus) }}</p>
            <p><strong>Submitted At:</strong> {{ \Carbon\Carbon::parse($inquiry->SubmissionDate)->format('d M Y') }}</p>
            <p><strong>Agency:</strong> Unassigned</p>
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn-save">Save Category</button>
            <a href="{{ route('mcmc.assign.agency', $inquiry->InquiryID) }}" class="btn-next">Next</a>
        </div>
    </form>
</div>
@endsection
