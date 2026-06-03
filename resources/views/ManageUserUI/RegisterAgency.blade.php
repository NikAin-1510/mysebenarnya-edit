@extends('layouts.layout')
@section('page-name', 'Register Agency')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module1/registeragency.css') }}">
@endpush

@section('page-name', 'Agency Staff Registration')

@section('content')
<div class="registration-container">
    <form method="POST" action="{{ route('register.agency') }}" enctype="multipart/form-data" class="registration-card">
        @csrf

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="form-error">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Flash Success --}}
        @if (session('success'))
            <div class="form-success">{{ session('success') }}</div>
        @endif

        <h2 class="registration-title">Register New Agency Staff</h2>

        <div class="form-field">
            <label>Full Name:</label>
            <input type="text" name="Name" placeholder="Full Name" value="{{ old('Name') }}" required>
        </div>

        <div class="form-field">
            <label>Email:</label>
            <input type="email" name="Email" placeholder="Email Address" value="{{ old('Email') }}" required>
        </div>

        <div class="form-field">
            <label>Phone Number:</label>
            <input type="text" name="PhoneNum" placeholder="Optional" value="{{ old('PhoneNum') }}">
        </div>

        <div class="form-field">
            <label>Agency Name:</label>
            <input type="text" name="AgencyName" placeholder="Agency Name" value="{{ old('AgencyName') }}" required>
        </div>

        {{-- Hidden Role --}}
        <input type="hidden" name="Role" value="mcmc">

        <p class="info-text">Password will be generated automatically and sent to the agency staff.</p>

        <div class="button-group">
            <button type="submit" class="btn-save">Register</button>
            <a href="{{ route('view.profile') }}" class="btn-back">Back</a>
        </div>
    </form>
</div>
@endsection
