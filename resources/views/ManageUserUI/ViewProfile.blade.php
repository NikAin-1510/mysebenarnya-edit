@php
    $mime = finfo_buffer(finfo_open(), $user->ProfilePic, FILEINFO_MIME_TYPE);
@endphp

@extends('layouts.layout')
@section('page-name', 'View Profile')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module1/viewprofile.css') }}">
@endpush

@section('content')
<div class="profile-container">
    <div class="profile-card">
        <div class="profile-header">
            @if($user->ProfilePic)
                <img src="data:{{ $mime }};base64,{{ base64_encode($user->ProfilePic) }}" alt="Profile Picture" class="profile-pic">
            @else
                @switch($user->Role)
                    @case('publicuser')
                        <img src="{{ asset('images/public.png') }}" alt="Public Default Profile" class="profile-pic">
                        @break
                    @case('mcmc')
                        <img src="{{ asset('images/mcmc.png') }}" alt="MCMC Default Profile" class="profile-pic">
                        @break
                    @case('agency')
                        <img src="{{ asset('images/agency.jpeg') }}" alt="Agency Default Profile" class="profile-pic">
                        @break
                    @default
                        <img src="{{ asset('images/default-profile.png') }}" alt="Default Profile" class="profile-pic">
                @endswitch
            @endif
        </div>

        <div class="profile-field"><strong>Full Name:</strong> {{ $user->Name }}</div>
        <div class="profile-field"><strong>Email:</strong> {{ $user->Email }}</div>
        <div class="profile-field"><strong>Phone Number:</strong> {{ $user->PhoneNum ?? 'Not provided' }}</div>

        @if($user->Role === 'publicuser')
            <div class="profile-field"><strong>Gender:</strong> {{ $user->publicuser->Gender ?? 'Not specified' }}</div>
        @elseif($user->Role === 'mcmc')
            <div class="profile-field"><strong>Position:</strong> {{ $user->mcmc->Position ?? 'Not specified' }}</div>
        @elseif($user->Role === 'agency')
            <div class="profile-field"><strong>Agency Name:</strong> {{ $user->agency->AgencyName ?? 'Not specified' }}</div>
        @endif

        {{-- NEW BUTTONS --}}
        <div class="profile-buttons">
            @if(session('user_role') === 'publicuser' || session('user_role') === 'agency')
                <a href="{{ route('edit.profile') }}" class="btn btn-edit">Edit Profile</a>
                <a href="{{ route('showUpdateSecurityForm.security') }}" class="btn btn-security">Update Security</a>
            @endif
        </div>
    </div>
</div>
@endsection
