@extends('layouts.layout')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module2/list-inquiry.css') }}">
@endpush

@section('content')
<div class="container">
    <h1>Newly Submitted Inquiries</h1>
    <p>Below is the list of all new inquiries submitted by public users that haven't been assigned yet.</p>
    
    {{-- Success Message --}}
    @if(session('success'))
        <div class="success-message">
            <span class="icon">✓</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Error Message --}}
    @if(session('error'))
        <div class="error-message">
            <span class="icon">⚠</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Inquiry ID</th>
                    <th>News Title</th>
                    <th>Submission Date</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($inquiries as $inquiry)
                    <tr class="fade-in">
                        <td>{{ $inquiry->InquiryID }}</td>
                        <td>{{ Str::limit($inquiry->InquiryTitle, 50) }}</td>
                        <td>{{ \Carbon\Carbon::parse($inquiry->SubmissionDate)->format('d M Y, h:i A') }}</td>
                        <td>
                            @if ($inquiry->SubmissionCategory === 'Genuine')
                                <span class="badge badge-genuine">Genuine</span>
                            @elseif ($inquiry->SubmissionCategory === 'Non-Serious')
                                <span class="badge badge-nonserious">Non-Serious</span>
                            @else
                                <span class="badge">{{ $inquiry->SubmissionCategory ?? 'Uncategorized' }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge">
                                {{ ucfirst($inquiry->SubmissionStatus) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('inquiry.assign.view', $inquiry->InquiryID) }}" 
                               class="btn btn-primary">
                                Assign to Agency
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-state">
                            <div class="empty-state-icon">📋</div>
                            <h3>No New Inquiries</h3>
                            <p>All submitted inquiries have been assigned to agencies.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($inquiries->count() > 0)
        <div class="summary-box">
            <strong>📊 Summary:</strong> 
            {{ $inquiries->count() }} {{ Str::plural('inquiry', $inquiries->count()) }} waiting for assignment
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide success message after 5 seconds
        const successMessage = document.querySelector('.success-message');
        if (successMessage) {
            setTimeout(function() {
                successMessage.style.transition = 'opacity 0.5s ease';
                successMessage.style.opacity = '0';
                setTimeout(function() {
                    successMessage.remove();
                }, 500);
            }, 5000);
        }

        // Auto-hide error message after 8 seconds
        const errorMessage = document.querySelector('.error-message');
        if (errorMessage) {
            setTimeout(function() {
                errorMessage.style.transition = 'opacity 0.5s ease';
                errorMessage.style.opacity = '0';
                setTimeout(function() {
                    errorMessage.remove();
                }, 500);
            }, 8000);
        }

        // Add loading state to assignment buttons
        const assignButtons = document.querySelectorAll('.btn-primary');
        assignButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                this.style.opacity = '0.6';
                this.style.pointerEvents = 'none';
                this.textContent = 'Loading...';
            });
        });

        // Refresh page every 30 seconds to check for new inquiries
        setInterval(function() {
            // Only refresh if user is not interacting with the page
            if (document.hidden === false) {
                const currentTime = Date.now();
                const lastActivity = localStorage.getItem('lastActivity') || currentTime;
                
                // Refresh if no activity for 30 seconds
                if (currentTime - lastActivity > 30000) {
                    window.location.reload();
                }
            }
        }, 30000);

        // Track user activity
        let activityEvents = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'];
        activityEvents.forEach(event => {
            document.addEventListener(event, function() {
                localStorage.setItem('lastActivity', Date.now());
            });
        });
    });

    // Function to manually refresh the list
    function refreshInquiries() {
        window.location.reload();
    }
</script>
@endpush
@endsection