@extends('layouts.layout')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/module4/agency-inquiry.css') }}">
@endsection

@section('content')
    <div class="assigned-inquiry-list">
    <table>
        <tr>
            <th style="width: 35%;">Inquiry</th>
            <th>Date Assigned</th>
            <th>Current Status</th>
        </tr>
        @forelse($assignedInquiries as $inquiry)
        <tr>
            <td><a href="{{ url('/agency/updatestatus?id=' . $inquiry->InquiryID) }}">{{ $inquiry->InquiryTitle }}</a></td>
            <td>{{ $inquiry->AssignDate }}</td>
            <td>
                @if($inquiry->VerificationStatus)
                    {{ $inquiry->VerificationStatus }}
                @elseif($inquiry->InvestigationBeginDate)
                    Under Investigation
                @else
                    Pending
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="3">No inquiries assigned to you yet.</td>
        </tr>
        @endforelse
    </table>
</div>
@endsection
