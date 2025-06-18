@php
    $mime = finfo_buffer(finfo_open(), $user->ProfilePic, FILEINFO_MIME_TYPE);
@endphp

@extends('layouts.layout')
@section('page-name', 'Edit Profile')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module1/editprofile.css') }}">
@endpush

@section('content')
<div class="profile-container">
    <form action="{{ route('edit.profile.save') }}" method="POST" enctype="multipart/form-data" class="profile-card">
        @csrf
        @method('PUT')

        <div class="profile-header">
            <label for="profile_pic" class="profile-pic-label">
                @if($user->ProfilePic)
                    <img src="data:{{ $mime }};base64,{{ base64_encode($user->ProfilePic) }}" alt="Profile Picture" class="profile-pic">
                @else
                    @if($user->Role === 'publicuser')
                        <img src="{{ asset('images/public.png') }}" alt="Public Default Profile" class="profile-pic">
                    @elseif($user->Role === 'mcmc')
                        <img src="{{ asset('images/mcmc.png') }}" alt="MCMC Default Profile" class="profile-pic">
                    @elseif($user->Role === 'agency')
                        <img src="{{ asset('images/agency.jpeg') }}" alt="Agency Default Profile" class="profile-pic">
                    @else
                        <img src="{{ asset('images/default-profile.png') }}" alt="Default Profile" class="profile-pic">
                    @endif
                @endif
                <div class="edit-icon">&#9998;</div> {{-- pencil icon --}}
            </label>
            <input type="file" name="ProfilePic" id="profile_pic" class="hidden-file">
        </div>

        <div class="profile-field">
            <strong>Full Name:</strong>
            <input type="text" name="Name" value="{{ $user->Name }}" required>
        </div>

        <div class="profile-field">
            <strong>Email:</strong>
            <input type="email" name="Email" value="{{ $user->Email }}" required>
        </div>

        <div class="profile-field">
            <strong>Phone Number:</strong>
            <input type="text" name="PhoneNum" value="{{ $user->PhoneNum }}">
        </div>

        @if($user->Role == 'publicuser')
            <div class="profile-field">
                <strong>Gender:</strong>
                <select name="Gender">
                    <option value="">-- Select Gender --</option>
                    <option value="male" {{ optional($user->publicuser)->Gender == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ optional($user->publicuser)->Gender == 'female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
        @elseif($user->Role == 'mcmc')
            <div class="profile-field">
                <strong>Position:</strong>
                <input type="text" name="Position" value="{{ optional($user->mcmc)->Position }}">
            </div>
        @elseif($user->Role == 'agency')
            <div class="profile-field">
                <strong>Agency Name:</strong>
                <input type="text" name="AgencyName" value="{{ optional($user->agency)->AgencyName }}">
            </div>
        @endif

        <div class="profile-field button-group">
            <button type="submit" class="btn-save">Save Changes</button>
            <a href="{{ route('view.profile') }}" class="btn-back">Back</a>
        </div>
    </form>
</div>
@endsection

<script>
    document.getElementById('profile_pic').addEventListener('change', function(event) {
        const reader = new FileReader();
        const file = event.target.files[0];

        reader.onload = function(e) {
            document.querySelector('.profile-pic').src = e.target.result;
        };

        if (file) {
            reader.readAsDataURL(file);
        }
    });
</script>

