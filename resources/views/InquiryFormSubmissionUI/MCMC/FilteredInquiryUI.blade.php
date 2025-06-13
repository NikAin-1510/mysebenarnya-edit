@extends('layouts.layout')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module2/inquiryform.css') }}">
@endpush
@section('content')
<div class="content">
    <div class="content-header">
        <h1>Previous Inquiries</h1>
        <p>View and download filtered past inquiries</p>
    </div>

    <div class="card">
        <form method="GET" action="{{ route('inquiries.index') }}" class="filters">
            <div class="filter-group">
                <label for="date">Submission Date</label>
                <input type="date" name="date" id="date" value="{{ request('date') }}">
            </div>
            <div class="filter-group">
                <label for="status">Status</label>
                <select name="status" id="status">
                    <option value="">All</option>
                    <option value="forwarded" {{ request('status') == 'forwarded' ? 'selected' : '' }}>Forwarded</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="agency">Agency</label>
                <select name="agency" id="agency">
                    <option value="">All</option>
                    @foreach($agencies as $agency)
                        <option value="{{ $agency->AgencyID }}" {{ request('agency') == $agency->AgencyID ? 'selected' : '' }}>
                            {{ $agency->AgencyName }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group" style="align-self: end;">
                <button type="submit" class="btn">Filter</button>
            </div>
        </form>

        <div class="download-buttons">
            <form method="GET" action="{{ route('inquiries.download') }}">
                <input type="hidden" name="date" value="{{ request('date') }}">
                <input type="hidden" name="status"
