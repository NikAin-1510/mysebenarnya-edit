@extends('layouts.layout')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/module4/agency-inquiry.css') }}">
@endsection

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
                    <td><a href="{{ url('/public/allinquiries?id=' . $notif->InquiryID) }}">{{ $notif->InquiryTitle }}</a></td>
                    <td>
                        @if ($notif->VerificationStatus)
                            {{ $notif->VerificationStatus }}
                        @elseif ($notif->InvestigationBeginDate)
                            Under Investigation Started
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if ($notif->VerificationDateTime)
                            {{ \Carbon\Carbon::parse($notif->VerificationDateTime)->format('Y-m-d H:i') }}
                        @elseif ($notif->InvestigationBeginDate)
                            {{ \Carbon\Carbon::parse($notif->InvestigationBeginDate)->format('Y-m-d H:i') }}
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
