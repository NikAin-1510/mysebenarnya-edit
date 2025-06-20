@extends('layouts.layout')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module3/assign-inquiry.css') }}">
@endpush

@section('content')
<div class="container">
    <h1>Inquiry Details</h1>
    
    <form action="{{ route('agency.assign.inquiry', $inquiry->InquiryID) }}" method="POST" id="assignmentForm">
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
            <p><strong>Agency:</strong>
                <select name="AgencyID" class="category-select" required>
                    <option value="">-- Select Agency --</option>
                    @foreach($agencies as $agency)
                        <option value="{{ $agency->AgencyID }}">{{ $agency->AgencyName }}</option>
                    @endforeach
                </select>
                @error('AgencyID')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </p>
            
            {{-- Inquiry Comment --}}
            <p><strong>Comment:</strong>
                <textarea name="InquiryComment" rows="3" class="category-select" required placeholder="Enter assignment comment..."></textarea>
                @error('InquiryComment')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </p>
        </div>
        
        <div class="text-center mt-4">
            <br>
            <a href="{{ route('mcmc.new.inquiry') }}" class="btn-back">Back to List</a>
            <button type="submit" class="btn-save">Save Assignment</button>
        </div>
    </form>
</div>

<!-- Success Popup -->
<div class="popup-overlay" id="successPopup">
    <div class="popup-content">
        <div class="popup-icon">
            ✓
        </div>
        <div class="popup-title">Assignment Successful!</div>
        <div class="popup-message">
            The inquiry has been successfully assigned to the selected agency.
        </div>
        <button class="popup-btn" onclick="redirectToList()">Continue</button>
    </div>
</div>

@push('scripts')
<script>
    // Check if there's a success message from the server
    @if(session('success'))
        // Show popup immediately when page loads
        document.addEventListener('DOMContentLoaded', function() {
            showSuccessPopup();
        });
    @endif

    // Handle form submission with loading state
    document.getElementById('assignmentForm').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('.btn-save');
        const originalText = submitBtn.textContent;
        
        // Add loading state
        submitBtn.textContent = 'Assigning...';
        submitBtn.disabled = true;
        submitBtn.classList.add('loading');
        
        // Reset button if form submission fails
        setTimeout(function() {
            if (submitBtn.disabled) {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
            }
        }, 10000); // Reset after 10 seconds if no response
    });

    function showSuccessPopup() {
        const popup = document.getElementById('successPopup');
        popup.classList.add('show');
        
        // Auto-redirect after 3 seconds if user doesn't click
        setTimeout(function() {
            if (popup.classList.contains('show')) {
                redirectToList();
            }
        }, 3000);
    }

    function redirectToList() {
        window.location.href = "{{ route('mcmc.new.inquiry') }}";
    }

    // Show error messages if any
    @if(session('error'))
        alert('Error: {{ session('error') }}');
    @endif

    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('assignmentForm');
        const agencySelect = form.querySelector('select[name="AgencyID"]');
        const commentTextarea = form.querySelector('textarea[name="InquiryComment"]');
        
        // Real-time validation
        agencySelect.addEventListener('change', function() {
            if (this.value) {
                this.classList.remove('error');
            }
        });
        
        commentTextarea.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('error');
            }
        });
        
        // Form submission validation
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            if (!agencySelect.value) {
                agencySelect.classList.add('error');
                isValid = false;
            }
            
            if (!commentTextarea.value.trim()) {
                commentTextarea.classList.add('error');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });
</script>
@endpush
@endsection