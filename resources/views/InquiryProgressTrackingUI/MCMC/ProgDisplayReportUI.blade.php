@extends('layouts.layout')

@section('content')
<div class="container">
    <h2>Agency Performance Report</h2>

    <form method="POST" action="{{ route('mcmc.reports.generate') }}">
        @csrf
        <div>
            <label>Date From:</label>
            <input type="date" name="from" value="{{ old('from') ?? request('from') }}" required>
            <label>Date To:</label>
            <input type="date" name="to" value="{{ old('to') ?? request('to') }}" required>
        </div>
        <div>
            <label>Agency:</label>
            <select name="agency">
                <option value="">All</option>
                @foreach($agencies as $a)
                    <option value="{{ $a->AgencyID }}" @if(request('agency') == $a->AgencyID) selected @endif>
                        {{ $a->AgencyName }}
                    </option>
                @endforeach
            </select>

            <label>Status:</label>
            <select name="status">
                <option value="all" @if(request('status') == 'all') selected @endif>All</option>
                <option value="Pending" @if(request('status') == 'Pending') selected @endif>Pending</option>
                <option value="Verified as True" @if(request('status') == 'Verified as True') selected @endif>Verified as True</option>
                <option value="Identified as Fake" @if(request('status') == 'Identified as Fake') selected @endif>Identified as Fake</option>
                <option value="Rejected" @if(request('status') == 'Rejected') selected @endif>Rejected</option>
            </select>
        </div>

        <button type="submit">Generate Report</button>
    </form>

    @isset($report)
    <hr>
    <h3>Report Results</h3>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Agency</th>
                <th>Total Assigned</th>
                <th>Resolved</th>
                <th>Pending</th>
                <th>Avg. Resolution Time (days)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report as $agency => $data)
            <tr>
                <td>{{ $agency }}</td>
                <td>{{ $data['assigned'] }}</td>
                <td>{{ $data['resolved'] }}</td>
                <td>{{ $data['pending'] }}</td>
                <td>{{ number_format($data['avgTime'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    <div>
        <a href="{{ route('mcmc.reports.excel', request()->all()) }}" class="btn">Download Excel</a>
        <a href="{{ route('mcmc.reports.pdf', request()->all()) }}" class="btn">Download PDF</a>
    </div>

    <br>
    <canvas id="agencyChart" width="800" height="400"></canvas>

    @endif
    @endisset
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@if(isset($report))
<script>
    const chartLabels = {!! json_encode($report->keys()) !!};
    const assigned = {!! json_encode($report->map->assigned->values()) !!};
    const resolved = {!! json_encode($report->map->resolved->values()) !!};
    const pending = {!! json_encode($report->map->pending->values()) !!};

    const ctx = document.getElementById('agencyChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [
                {
                    label: 'Assigned',
                    data: assigned,
                    backgroundColor: '#007bff'
                },
                {
                    label: 'Resolved',
                    data: resolved,
                    backgroundColor: '#28a745'
                },
                {
                    label: 'Pending',
                    data: pending,
                    backgroundColor: '#ffc107'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            title: { display: true, text: 'Agency Inquiry Performance' }
        }
    });
</script>
@endif
@endsection
