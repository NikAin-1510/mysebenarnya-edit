@extends('layouts.layout')
@section('page-name', 'Generate Inquiry Reports')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module3/report.css') }}">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="container">
    <h1>Inquiry Assignment Reports</h1>

    <form method="GET" action="{{ route('mcmc.report') }}" class="filter-form">
        <div class="form-group">
            <label>Start Date:</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}">
        </div>
        <div class="form-group">
            <label>End Date:</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}">
        </div>
        <div class="form-group">
            <label>Agency:</label>
            <select name="agency_id">
                <option value="">All Agencies</option>
                @foreach ($agencies as $agency)
                    <option value="{{ $agency->AgencyID }}" {{ request('agency_id') == $agency->AgencyID ? 'selected' : '' }}>
                        {{ $agency->AgencyName }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-filter">Filter</button>
    </form>

    <div class="chart-container">
        <canvas id="assignmentChart"></canvas>
    </div>

    <div class="download-buttons">
        <a href="{{ route('mcmc.report.export.pdf') }}" class="btn-download">Download PDF</a>
        <a href="{{ route('mcmc.report.export.excel') }}" class="btn-download">Download Excel</a>
    </div>
</div>

<script>
    const ctx = document.getElementById('assignmentChart').getContext('2d');

const colors = {!! json_encode($agencyNames) !!}.map(() => {
    return '#' + Math.floor(Math.random()*16777215).toString(16).padStart(6, '0');
});

const assignmentChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($agencyNames) !!},
        datasets: [{
            label: 'Total Assigned Inquiries',
            data: {!! json_encode($agencyCounts) !!},
            backgroundColor: colors,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            title: {
                display: true,
                text: 'Inquiry Distribution per Agency',
                font: { size: 20 }
            }
        },
        scales: {
            y: { ticks: { stepSize: 1 } }
        }
    }
});
</script>
@endsection
