@extends('layouts.layout')

@section('page-name', 'Track Cases')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module3/agency.css') }}">
@endpush

@section('content')
    <h2><i class="fas fa-tasks"></i> Track Cases - Assigned Inquiries</h2>
    <p>Review and manage inquiries assigned by MCMC for verification</p>

    <div class="inquiry-grid">
        @foreach($assignments as $assign)
            <div class="inquiry-card" onclick="window.location='{{ route('agency.inquiry.details', $assign->AssignmentID) }}'">
                <div class="inquiry-card-header">
                    <h4>{{ $assign->inquiry->InquiryTitle }}</h4>
                    <div class="inquiry-card-meta">
                        <span><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($assign->AssignDate)->format('Y-m-d') }}</span>
                        <span><i class="fas fa-user"></i> {{ $assign->AgencyName ?? '-' }}</span>
                    </div>
                </div>
                <div class="inquiry-card-body">
                    <p><strong>ID:</strong> {{ $assign->InquiryID }}</p>
                    <p><strong>Status:</strong>
                        <span class="inquiry-status status-{{ strtolower($assign->progress->VerificationStatus ?? 'pending') }}">
                            {{ strtoupper($assign->progress->VerificationStatus ?? 'PENDING') }}
                        </span>
                    </p>
                </div>
            </div>
        @endforeach
    </div>
@endsection
