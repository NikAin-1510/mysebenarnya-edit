@extends('layouts.layout')

@section('content')
<div class="report-container">
    <div class="header">
        <h1>Inquiry Reports</h1>
        <p>Generate and filter reports based on public inquiries received.</p>
    </div>

    <form action="{{ route('mcmc.review.inquiry') }}" method="GET">

        <div class="filters">
            <div class="form-group">
                <label for="month">Month</label>
                <select name="month" id="month" class="form-control">
                    <option value="">All</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="year">Year</label>
                <select name="year" id="year" class="form-control">
                    <option value="">All</option>
                    @foreach(range(now()->year, 2020) as $year)
                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="agency">Agency</label>
                <select name="agency" id="agency" class="form-control">
                    <option value="">All</option>
                    @foreach($agencies as $agency)
                        <option value="{{ $agency->AgencyID }}" {{ request('agency') == $agency->AgencyID ? 'selected' : '' }}>
                            {{ $agency->AgencyName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group submit-btn">
                <button type="submit" class="btn btn-primary">Generate</button>
            </div>
        </div>
    </form>

    <div class="report-table">
        <h2>Report Results</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Inquiry ID</th>
                    <th>Title</th>
                    <th>Submission Date</th>
                    <th>Agency</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inquiries as $inquiry)
                    <tr>
                        <td>{{ $inquiry->InquiryID }}</td>
                        <td>{{ $inquiry->InquiryTitle }}</td>
                        <td>{{ \Carbon\Carbon::parse($inquiry->SubmissionDate)->format('d M Y') }}</td>
                        <td>{{ $inquiry->AgencyName ?? 'Unassigned' }}</td>
                        <td>{{ ucfirst($inquiry->SubmissionStatus) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No data found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
