@php
    $mime = finfo_buffer(finfo_open(), $user->ProfilePic, FILEINFO_MIME_TYPE);
@endphp

@extends('layouts.layout')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module1/viewuserdata.css') }}">
@endpush

@section('page-name', 'View User Data')

@section('content')
<div class="user-data-container">

    {{-- LEFT COLUMN ──────────────── --}}
    <div class="profile-card">
        <div class="profile-header">
            <h3>Profile Information</h3><br>
            @if($user->ProfilePic)
                <img src="data:{{ $mime }};base64,{{ base64_encode($user->ProfilePic) }}" alt="Profile Picture" class="profile-pic">
            @else
                @switch($user->Role)
                    @case('publicuser')
                        <img src="{{ asset('images/public.png') }}"  class="profile-pic" alt="Public Default">
                        @break
                    @case('agency')
                        <img src="{{ asset('images/agency.jpeg') }}" class="profile-pic" alt="Agency Default">
                        @break
                    @default
                        <img src="{{ asset('images/default-profile.png') }}" class="profile-pic" alt="Default Profile">
                @endswitch
            @endif
        </div>

        <div class="profile-field"><span class="label">Full Name:</span> {{ $user->Name }}</div>
        <div class="profile-field"><span class="label">Email:</span> {{ $user->Email }}</div>
        <div class="profile-field"><span class="label">Phone Number:</span> {{ $user->PhoneNum ?? 'Not provided' }}</div>

        @if($user->Role === 'publicuser')
            <div class="profile-field"><span class="label">Gender:</span> {{ $user->publicuser->Gender ?? 'Not specified' }}</div>
        @elseif($user->Role === 'agency')
            <div class="profile-field"><span class="label">Agency Name:</span> {{ $user->agency->AgencyName ?? 'Not specified' }}</div>
        @endif
    </div>

    {{-- RIGHT COLUMN ─────────────── --}}
    <div class="side-column">

        {{-- Registration card --}}
        <div class="side-card">
            <h3>Registration Details</h3>
            <p><span class="label">Registered on:</span><br>
                {{ $user->Created_At ? \Carbon\Carbon::parse($user->Created_At)->format('d M Y · H:i') : '—' }}
            </p>
        </div>

        {{-- Activity log card --}}
        <div class="side-card">
            <h3>Activity Logs</h3>
            <p><span class="label">Last login:</span><br>
                {{ $user->Login_At ? \Carbon\Carbon::parse($user->Login_At)->format('d M Y · H:i') : 'Never' }}
            </p><br>
            <p><span class="label">Last profile update:</span><br>
                {{ $user->Updated_At ? \Carbon\Carbon::parse($user->Updated_At)->format('d M Y · H:i') : '—' }}
            </p>
        </div>

    </div>
</div>
@endsection
