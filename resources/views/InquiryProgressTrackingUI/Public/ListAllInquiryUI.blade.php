@extends('layouts.layout')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/module4/public.css') }}">
@endsection

@section('content')
    <div class="inquiry-list">
        <h2>All Inquiries</h2>

        <form method="GET" action="/public/inquiry-list" style="margin-bottom: 20px;">

            <label for="status">Filter by Status:</label>
            <select name="status" id="status">
                <option value="">-- All --</option>
                <option value="Under Investigation" {{ request('status') == 'Under Investigation' ? 'selected' : '' }}>Under Investigation</option>
                <option value="Verified as True" {{ request('status') == 'Verified as True' ? 'selected' : '' }}>Verified as True</option>
                <option value="Identified as Fake" {{ request('status') == 'Identified as Fake' ? 'selected' : '' }}>Identified as Fake</option>
            </select>

            <label>
                <input type="checkbox" name="own_only" {{ request('own_only') ? 'checked' : '' }}>
                Show My Submissions Only
            </label>

            <button type="submit">Filter</button>
        </form>

        <table>
            <tr>
                <th style="width: 40%;">Inquiry Title</th>
                <th>Submission Date</th>
                <th>Status</th>
            </tr>
            @forelse($inquiries as $inq)
                <tr>
                    <td><a href="{{ route('details.all.inquiry', ['id' => $inq->InquiryID]) }}">
    {{ $inq->InquiryTitle }}
</a></td>
                    <td>{{ \Carbon\Carbon::parse($inq->SubmissionDate)->format('Y-m-d H:i') }}</td>
                   @php
    if (!empty($inq->progress?->VerificationStatus)) {
        $status = 'Completed';
    } elseif (!empty($inq->progress?->InvestigationBeginDate)) {
        $status = 'Under Investigation';
    } elseif (!empty($inq->latestAssignment)) {
        $status = 'Forwarded';
    } else {
        $status = 'Pending';
    }
@endphp

                    <td>{{ $status }}</td>

                </tr>
            @empty
                <tr>
                    <td colspan="3">No inquiries found.</td>
                </tr>
            @endforelse
        </table>
    </div>
@endsection
