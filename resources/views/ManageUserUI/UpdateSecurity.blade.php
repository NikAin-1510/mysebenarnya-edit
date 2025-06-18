@extends('layouts.layout')
@section('page-name', 'Update Security')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module1/updatesecurity.css') }}">
@endpush

@section('page-name', 'Update Security')
@section('content')
<div class="profile-container">
    <form action="{{ route('update.security') }}" method="POST" class="profile-card">
        @csrf
        @method('PUT')

        <div class="profile-field">
            <strong>Current Password:</strong>
            <input type="password" name="current_password" required>
        </div>

        <div class="profile-field">
            <strong>New Password:</strong>
            <input type="password" name="new_password" required>
        </div>

        <div class="profile-field">
            <strong>Confirm New Password:</strong>
            <input type="password" name="new_password_confirmation" required>
        </div>

        <div class="button-group">
            <button type="submit" class="btn-save">Update Password</button>
            <a href="{{ route('view.profile') }}" class="btn-back">Back</a>
        </div>
    </form>
</div>
@endsection
