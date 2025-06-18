@extends('layouts.layout')
@section('page-name', 'View All Users')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module1/viewallusers.css') }}">
@endpush

@section('page-name', 'View All Users')

@section('content')
<div class="users-container">
    <div class="users-table-wrapper">
        <table class="slot-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->Name }}</td>
                        <td>{{ ucfirst($user->Role) }}</td>
                        <td>
                            <a href="{{ route('view.user.data', $user->UserID) }}" class="view-btn">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center;">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
