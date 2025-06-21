@extends('layouts.layout')

@section('page-name', 'Assigned Inquiries')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Module3/agency-inquiry-list.css') }}">
@endpush

@section('content')
    <h2><i class="fas fa-tasks"></i> Assigned Inquiries</h2>
    <p>Below are the inquiries assigned to your agency for investigation and verification:</p>

    <div class="inquiry-grid">
        @forelse($assignments as $assign)
            <div class="inquiry-card" onclick="window.location='{{ route('agency.inquiry.details', $assign->AssignmentID) }}'">
                <div class="inquiry-card-header">
                    <h4>{{ $assign->inquiry?->InquiryTitle ?? '-' }}</h4>
                    <div class="inquiry-card-meta">
                        <span><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($assign->AssignDate)->format('Y-m-d') }}</span>
                        <span><i class="fas fa-user"></i> {{ $assign->agency->AgencyName }}</span>
                    </div>
                </div>
                <div class="inquiry-card-body">
                    <p><strong>ID:</strong> {{ $assign->InquiryID }}</p>
                    <p><strong>Status:</strong>
                        <span class="inquiry-status status-{{ strtolower(str_replace(' ', '-', $assign->progress->VerificationStatus ?? 'Pending')) }}">
                            {{ strtoupper($assign->progress->VerificationStatus ?? 'PENDING') }}
                        </span>
                    </p>
                </div>
            </div>
        @empty
            <p>No assigned inquiries found.</p>
        @endforelse
    </div>
@endsection
