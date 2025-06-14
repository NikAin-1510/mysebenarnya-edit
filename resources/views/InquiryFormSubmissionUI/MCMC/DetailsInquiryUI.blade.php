@extends('layouts.layout')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module2/mcmc-details-inquiry.css') }}">
@endpush
@section('content')
<div class="container">
    <h1>Inquiry Details</h1>

    <div class="card p-4 mb-3">
        <h3>{{ $inquiry->InquiryTitle }}</h3>
        <p><strong>Description:</strong> {{ $inquiry->Description }}</p>
        <p><strong>Link:</strong> <a href="{{ $inquiry->URL }}" target="_blank">{{ $inquiry->URL }}</a></p>
        <p><strong>Evidence:</strong>
            @if($inquiry->Evidence)
                <a href="{{ asset('storage/evidence/' . $inquiry->Evidence) }}" target="_blank">View File</a>
            @else
                No file uploaded.
            @endif
        </p>

        <form action="{{ route('inquiry.update.category', $inquiry->PublicID) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="SubmissionCategory">Category:</label>
                <select name="SubmissionCategory" id="SubmissionCategory" class="form-control" onchange="toggleNextButton()">
                    <option value="">-- Select Category --</option>
                    <option value="Genuine" {{ $inquiry->SubmissionCategory == 'Genuine' ? 'selected' : '' }}>Genuine</option>
                    <option value="NonSerious" {{ $inquiry->SubmissionCategory == 'NonSerious' ? 'selected' : '' }}>Non-Serious</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success mt-3">Save</button>

            <a id="nextBtn" href="{{ route('inquiry.assign.agency', $inquiry->PublicID) }}" class="btn btn-primary mt-3"
                style="{{ $inquiry->SubmissionCategory === 'Genuine' ? '' : 'display: none;' }}">Next</a>
        </form>
    </div>
</div>

<script>
    function toggleNextButton() {
        const category = document.getElementById('SubmissionCategory').value;
        document.getElementById('nextBtn').style.display = category === 'Genuine' ? 'inline-block' : 'none';
    }
</script>
@endsection
