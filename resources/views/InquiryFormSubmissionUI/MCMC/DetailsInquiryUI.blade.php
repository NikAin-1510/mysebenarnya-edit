@extends('layouts.layout')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module2/mcmc-new-details.css') }}">
@endpush

@section('content')
<div class="container">
    <h1>Inquiry Details</h1>

    <form action="{{ route('mcmc.new.inquiry', $inquiry->InquiryID) }}" method="POST">
    @csrf

        <div class="inquiry-details-card">
            <span class="inquiry-id-badge">ID: {{ $inquiry->InquiryID }}</span>
            <p><strong>Title:</strong> {{ $inquiry->InquiryTitle }}</p>
            <p><strong>Description:</strong> {{ $inquiry->InquiryDescription }}</p>

            {{-- Dropdown for SubmissionCategory --}}
            <p><strong>Category:</strong>
                <select id="category-select" name="SubmissionCategory" class="category-select" required>
                    <option value="-" {{ $inquiry->SubmissionCategory == '-' ? 'selected' : '' }}>-</option>
                    <option value="Genuine" {{ $inquiry->SubmissionCategory == 'Genuine' ? 'selected' : '' }}>Genuine</option>
                    <option value="Non-Serious" {{ $inquiry->SubmissionCategory == 'Non-Serious' ? 'selected' : '' }}>Non-Serious</option>
                </select>
            </p>

            <p><strong>Status:</strong> {{ ucfirst($inquiry->SubmissionStatus) }}</p>
            <p><strong>Submitted At:</strong> {{ \Carbon\Carbon::parse($inquiry->SubmissionDate)->format('d M Y') }}</p>

            {{-- Evidence Section --}}
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

            <p><strong>Agency:</strong> Unassigned</p>

            <div class="text-center mt-4">
                <br>
                <a href="{{ route('public.list') }}" class="btn-back">Back to List</a>
                <button type="submit" class="btn-save">Save</button>
                <a href="{{ route('mcmc.assign.form', $inquiry->InquiryID) }}" class="btn-next" id="next-button">Next</a>
            </div>
        </div>
    </form>
</div>

{{-- JavaScript to show/hide the Next button --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const categorySelect = document.getElementById('category-select');
        const nextButton = document.getElementById('next-button');

        function toggleNextButton() {
            if (categorySelect.value === 'Genuine') {
                nextButton.style.display = 'inline-block';
            } else {
                nextButton.style.display = 'none';
            }
        }

        // Initial check
        toggleNextButton();

        // Add event listener
        categorySelect.addEventListener('change', toggleNextButton);
    });
</script>
@endsection
