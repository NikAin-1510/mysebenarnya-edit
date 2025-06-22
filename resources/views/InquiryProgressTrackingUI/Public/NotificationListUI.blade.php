@extends('layouts.layout')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/module4/agency-inquiry.css') }}">
@endsection
@section('page-name', 'Notification List')
@section('content')
    <div class="notification-list">
        <h2>Notification List</h2>

        <table>
            <tr>
                <th>Inquiry Title</th>
                <th>Status</th>
                <th>Date</th>
            </tr>

            @forelse($notifications as $notif)
    <tr>
        <td>
            <a href="{{ url('/public/notification-details?id=' . $notif->InquiryID . '&status=' . urlencode($notif->Status)) }}">
                {{ $notif->InquiryTitle }}
            </a>
        </td>
        <td>{{ $notif->Status }}</td>
        <td>
    @if ($notif->Status === 'Under Investigation')
        {{ \Carbon\Carbon::parse($notif->InvestigationBeginDate)->format('Y-m-d H:i') }}
    @elseif ($notif->Status)
        {{ \Carbon\Carbon::parse($notif->VerificationDateTime)->format('Y-m-d H:i') }}
    @else
        -
    @endif
</td>
    </tr>
@empty
    <tr>
        <td colspan="3">No notifications at the moment.</td>
    </tr>
@endforelse
        </table>
    </div>
@endsection
