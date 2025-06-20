@extends('layouts.layout')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module3/assign-inquiry.css') }}">
@endpush

@section('content')
<div class="container">
    <h1>Inquiry Details</h1>

    <form action="{{ route('agency.assign.inquiry', $inquiry->InquiryID) }}" method="POST">
        @csrf

        <div class="inquiry-details-card">
            <span class="inquiry-id-badge">ID: {{ $inquiry->InquiryID }}</span>
            <p><strong>Title:</strong> {{ $inquiry->InquiryTitle }}</p>
            <p><strong>Description:</strong> {{ $inquiry->InquiryDescription }}</p>

            <p><strong>Submission Date:</strong> {{ \Carbon\Carbon::parse($inquiry->SubmissionDate)->format('d M Y') }}</p>
            <p><strong>Submission Category:</strong> {{ $inquiry->SubmissionCategory }}</p>

            <div class="evidence-section">
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

            {{-- Agency Dropdown --}}
            <p><strong>Assign to Agency:</strong>
                <select name="AgencyID" class="category-select" required>
                    <option value="">-- Select Agency --</option>
                    @foreach($agencies as $agency)
                        <option value="{{ $agency->AgencyID }}">{{ $agency->AgencyName }}</option>
                    @endforeach
                </select>
            </p>

            {{-- Inquiry Comment --}}
            <p><strong>Comment:</strong>
                <textarea name="InquiryComment" rows="3" class="category-select" required></textarea>
            </p>

        </div>

        <div class="text-center mt-4">
            <br>
            <a href="{{ route('mcmc.new.inquiry') }}" class="btn-back">Back to List</a>
            <button type="submit" class="btn-save">Save</button>
        </div>
    </form>
</div>
@endsection
