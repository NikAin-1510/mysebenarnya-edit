@extends('layouts.layout')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module3/assign-inquiry.css') }}">
@endpush

@section('content')
<div class="container">
    <h1>Assign Inquiry</h1>
    
    <div class="card">
        <div class="card-header">
            Inquiry Details
        </div>
        <div class="card-body">
            <p><strong>Title:</strong> {{ $inquiry->InquiryTitle }}</p>
            <p><strong>Description:</strong> {{ $inquiry->InquiryDescription }}</p>
            <p><strong>Submission Date:</strong> {{ \Carbon\Carbon::parse($inquiry->SubmissionDate)->format('d M Y, h:i A') }}</p>
            <p><strong>Category:</strong> {{ $inquiry->SubmissionCategory }}</p>
            <p><strong>Submission Link:</strong> <a href="{{ $inquiry->SubmissionLink }}" target="_blank">{{ $inquiry->SubmissionLink }}</a></p>
        </div>
    </div>

    <form action="{{ route('agency.assign.inquiry', $inquiry->InquiryID) }}" method="POST">
        @csrf
        <div class="form-group mt-3">
            <label for="AgencyID">Assign to Agency:</label>
            <select class="form-control" name="AgencyID" required>
                <option value="">-- Select Agency --</option>
                @foreach($agencies as $agency)
                    <option value="{{ $agency->AgencyID }}">{{ $agency->AgencyName }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mt-3">
            <label for="InquiryComment">Comment:</label>
            <textarea class="form-control" name="InquiryComment" rows="3" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Assign Inquiry</button>
    </form>
</div>
@endsection
