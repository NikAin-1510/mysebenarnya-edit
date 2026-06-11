@extends('layouts.layout')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module2/list-assigned.css') }}">
@endpush

@section('content')
<div class="container">
    <h1 class="page-title">Verified & Fake Assigned Inquiries</h1>
    <p class="page-desc">Browse past inquiries your agency has reviewed and verified as either true or fake.</p>

    <form method="GET" action="{{ route('agency.list.assigned') }}" class="filter-form">
        <select name="status">
            <option value="">All Statuses</option>
            <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified True</option>
            <option value="fake" {{ request('status') == 'fake' ? 'selected' : '' }}>Identified as Fake</option>
        </select>

        <select name="category">
            <option value="">All Categories</option>
            <option value="Genuine" {{ request('category') == 'Genuine' ? 'selected' : '' }}>Genuine</option>
            <option value="NonSerious" {{ request('category') == 'NonSerious' ? 'selected' : '' }}>Non-Serious</option>
        </select>

        <input type="month" name="date" value="{{ request('date') }}">
        <input type="text" name="title" placeholder="Search by Inquiry Title" value="{{ request('title') }}">
        <button type="submit">Apply Filters</button>
    </form>

    <div class="inquiries-list">
        @forelse ($assignedInquiries as $inquiry)
            <div class="inquiry-card">
                <div class="inquiry-header">
                    <h3>{{ $inquiry->InquiryTitle }}</h3>
                    <span class="status-tag status-{{ strtolower($inquiry->latestProgress->VerificationStatus ?? 'unknown') }}">
                        {{ ucfirst($inquiry->latestProgress->VerificationStatus ?? 'Unknown') }}
                    </span>
                </div>

                <div class="inquiry-details">
                    <p><strong>Description:</strong> {{ $inquiry->InquiryDescription }}</p>
                    <p><strong>Category:</strong>
                        <span class="badge badge-{{ strtolower($inquiry->SubmissionCategory) }}">
                            {{ $inquiry->SubmissionCategory }}
                        </span>
                    </p>
                    <p><strong>Submitted On:</strong> {{ \Carbon\Carbon::parse($inquiry->SubmissionDate)->format('d M Y') }}</p>
                    <p><strong>Assigned On:</strong> {{ \Carbon\Carbon::parse($inquiry->latestAssignment->AssignDate)->format('d M Y') }}</p>
                    <p><strong>Verified On:</strong>
                        {{ $inquiry->latestProgress->VerificationDateTime
                            ? \Carbon\Carbon::parse($inquiry->latestProgress->VerificationDateTime)->format('d M Y')
                            : '-' }}
                    </p>
                </div>

                <div class="inquiry-footer">
                    <a href="{{ route('agency.inquiry.details', $inquiry->latestAssignment->AssignmentID) }}" class="btn-view">View Details</a>
                </div>
            </div>
        @empty
            <p>No inquiries found for the selected filters.</p>
        @endforelse
    </div>
</div>
@endsection
