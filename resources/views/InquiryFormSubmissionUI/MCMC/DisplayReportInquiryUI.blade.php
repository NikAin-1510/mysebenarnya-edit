@extends('layouts.layout')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module2/display-report.css') }}">
@endpush

@section('content')
<div class="report-container">
    <div class="header">
        <h1>Inquiry Report Dashboard</h1>
        <p>Generate, analyze, and export inquiry statistics</p>
    </div>

    {{-- Pie Chart --}}
    <div class="chart-section mb-5">
        <h2>Total Public Inquiries by Month (Pie)</h2>
        <div style="max-width: 400px; margin: 0 auto;">
            <canvas id="pieChart" width="400" height="400"></canvas>
        </div>
    </div>

    {{-- Bar Chart --}}
    <div class="chart-section mb-5">
        <h2>Total Public Inquiries by Month (Bar)</h2>
        <div style="max-width: 100%; overflow-x: auto;">
            <canvas id="barChart"></canvas>
        </div>
    </div>

    {{-- Filter Form --}}
    <form action="{{ route('show.totalInquiryReport') }}" method="GET" class="filter-form">
        <div class="filters">
            <div class="form-group">
                <label for="month">Month</label>
                <select name="month" class="form-control">
                    <option value="">All</option>
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="year">Year</label>
                <select name="year" class="form-control">
                    <option value="">All</option>
                    @foreach(range(now()->year, 2020) as $y)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="agency">Agency</label>
                <select name="agency" class="form-control">
                    <option value="">All</option>
                    @foreach($agencies as $agency)
                        <option value="{{ $agency->AgencyID }}" {{ request('agency') == $agency->AgencyID ? 'selected' : '' }}>
                            {{ $agency->AgencyName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    {{-- Export Buttons --}}
    <div class="export-buttons mb-4">
        <a href="{{ route('mcmc.export.pdf', request()->all()) }}" class="btn btn-danger">Export PDF</a>
        <a href="{{ route('mcmc.export.excel', request()->all()) }}" class="btn btn-success">Export Excel</a>
    </div>

    {{-- Table --}}
    <div class="report-table mt-5">
        <h2>Inquiry Records</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Submitted</th>
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
                    <tr><td colspan="5">No data available</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartData = @json($chartData);

    // Pie Chart
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Total Inquiries',
                data: chartData.data,
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e',
                    '#e74a3b', '#858796', '#5a5c69', '#20c9a6',
                    '#f8f9fc', '#e0aaff', '#b983ff', '#96f2d7'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Total Inquiries by Month'
                },
                legend: {
                    position: 'right'
                }
            }
        }
    });

    // Bar Chart
    const barCtx = document.getElementById('barChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Inquiries per Month',
                data: chartData.data,
                backgroundColor: '#4e73df'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
</script>
@endpush
