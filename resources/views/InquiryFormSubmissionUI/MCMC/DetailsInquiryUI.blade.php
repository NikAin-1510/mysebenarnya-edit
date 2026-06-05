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

            {{-- Dropdown for SubmissionCategory --}}
            <p><strong>Category:</strong>
                <select name="SubmissionCategory" class="form-control" id="category-select" required>
                    <option value="Serious" {{ $inquiry->SubmissionCategory == 'Serious' ? 'selected' : '' }}>Serious</option>
                    <option value="Non-Serious" {{ $inquiry->SubmissionCategory == 'Non-Serious' ? 'selected' : '' }}>Non-Serious</option>
                </select>
            </p>

            <p><strong>Status:</strong> {{ ucfirst($inquiry->SubmissionStatus) }}</p>
            <p><strong>Submitted At:</strong> {{ \Carbon\Carbon::parse($inquiry->SubmissionDate)->format('d M Y') }}</p>
            <p><strong>Agency:</strong> Unassigned</p>

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
            <a href="{{ route('mcmc.new.inquiry') }}" class="btn-back">Back to List</a>
            <button type="submit" class="btn-save">Save</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: '{{ session('success') }}',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'OK'
    });
</script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const categorySelect = document.getElementById('category-select');
        const nextButton = document.getElementById('next-button');

        function toggleNextButton() {
            if (categorySelect.value === 'Serious') {
                nextButton.style.display = 'inline-block';
            } else {
                nextButton.style.display = 'none';
            }
        }

        // Initialize on page load
        toggleNextButton();

        // Update on change
        categorySelect.addEventListener('change', toggleNextButton);
    });
</script>
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: '{{ session('success') }}',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'OK'
    });
</script>
@endif
@endpush
