@extends('layouts.layout')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module1/userreports.css') }}">

{{-- PDF-specific inline styling --}}
@if($pdf ?? false)
<style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 12px;
        color: #000;
    }

    h1 {
        text-align: center;
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    th, td {
        border: 1px solid #000;
        padding: 6px;
        text-align: left;
    }

    .total-row {
        margin-top: 20px;
        font-weight: bold;
    }
</style>
@endif
@endpush

@section('page-name', 'User Report')

@section('content')
@if(!($pdf ?? false))
<h1>User Report</h1>

<form method="GET" action="{{ route('show.registeredUserReport') }}">
    <label for="filter_by">Filter By:</label>
    <select name="filter_by" id="filter_by" onchange="showFilterOptions(this.value)">
        <option value="">-- Select Filter --</option>
        <option value="date" {{ request('filter_by') == 'date' ? 'selected' : '' }}>Registration Date</option>
        <option value="role" {{ request('filter_by') == 'role' ? 'selected' : '' }}>User Type</option>
        <option value="agency" {{ request('filter_by') == 'agency' ? 'selected' : '' }}>Agency</option>
    </select>

    <div id="filter-date" style="display: none;">
        <label for="date">Select Date:</label>
        <input type="date" name="date" value="{{ request('date') }}">
    </div>

    <div id="filter-role" style="display: none;">
        <label for="role">Select Role:</label>
        <select name="role">
            <option value="publicuser" {{ request('role') == 'publicuser' ? 'selected' : '' }}>Public User</option>
            <option value="mcmc" {{ request('role') == 'mcmc' ? 'selected' : '' }}>MCMC Staff</option>
        </select>
    </div>

    <div id="filter-agency" style="display: none;">
        <label for="agency">Select Agency:</label>
        <select name="agency">
            @foreach($agencies as $agency)
                <option value="{{ $agency->AgencyName }}" {{ request('agency') == $agency->AgencyName ? 'selected' : '' }}>
                    {{ $agency->AgencyName }}
                </option>
            @endforeach
        </select>
    </div>

    <button type="submit">Apply Filter</button>
</form>

<form method="GET" action="{{ route('userReport.download') }}">
    {{-- keep the filters on PDF download too --}}
    <input type="hidden" name="filter_by" value="{{ request('filter_by') }}">
    <input type="hidden" name="date" value="{{ request('date') }}">
    <input type="hidden" name="role" value="{{ request('role') }}">
    <input type="hidden" name="agency" value="{{ request('agency') }}">
    <button type="submit">Download PDF</button>
</form>
@endif

{{-- Table section works for both PDF and web --}}
<h2 style="text-align:center; margin-top:20px;">User Report Table</h2>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Role</th>
            <th>Registration Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($users as $index => $user)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $user->Name }}</td>
            <td>{{ ucfirst($user->Role) }}</td>
            <td>{{ \Carbon\Carbon::parse($user->Created_At)->format('Y-m-d') }}</td>
        </tr>
        @empty
        <tr><td colspan="4">No users found.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="total-row">Total Users: {{ $total }}</div>
@endsection

@push('scripts')
@if(!($pdf ?? false))
<script>
function showFilterOptions(value) {
    document.getElementById('filter-date').style.display = 'none';
    document.getElementById('filter-role').style.display = 'none';
    document.getElementById('filter-agency').style.display = 'none';

    if (value === 'date') {
        document.getElementById('filter-date').style.display = 'block';
    } else if (value === 'role') {
        document.getElementById('filter-role').style.display = 'block';
    } else if (value === 'agency') {
        document.getElementById('filter-agency').style.display = 'block';
    }
}
window.onload = function() {
    showFilterOptions(document.getElementById('filter_by').value);
};
</script>
@endif
@endpush
