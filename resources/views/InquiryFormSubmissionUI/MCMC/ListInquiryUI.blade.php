@extends('layouts.layout')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module2/list-inquiry.css') }}">
<style>
    .badge {
        display: inline-block;
        padding: 0.25em 0.6em;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 0.25rem;
        text-transform: capitalize;
        color: #fff;
    }
    .badge-genuine {
        background-color: #28a745; /* green */
    }
    .badge-nonserious {
        background-color: #dc3545; /* red */
    }
    .badge-secondary {
        background-color: #6c757d; /* grey */
    }
</style>
@endpush

@section('content')
<div class="container">
    <h1>Newly Submitted Inquiries</h1>
    <p>Below is the list of all new inquiries submitted by public users.</p>

    <table class="table">
        <thead>
            <tr>
                <th>News Title</th>
                <th>Submission Date</th>
                <th>Category</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($inquiries as $inquiry)
            <tr>
                <td>{{ $inquiry->InquiryTitle }}</td>
                <td>{{ \Carbon\Carbon::parse($inquiry->SubmissionDate)->format('d M Y, h:i A') }}</td>
                <td>
                    @if ($inquiry->SubmissionCategory === 'Genuine')
                        <span class="badge badge-genuine">Genuine</span>
                    @elseif ($inquiry->SubmissionCategory === 'Non-Serious')
                        <span class="badge badge-nonserious">Non-Serious</span>
                    @else
                        <span class="badge badge-secondary">Uncategorized</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('inquiry.own.view', $inquiry->InquiryID) }}" class="btn btn-primary">View Details</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4">No inquiries submitted yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
