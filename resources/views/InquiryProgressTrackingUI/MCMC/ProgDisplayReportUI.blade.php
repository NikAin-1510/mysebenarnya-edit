@extends('layouts.layout')

@section('content')
<div class="container">
    <h2>Agency Performance Report</h2>

    {{-- Hide form in PDF --}}
    @unless(isset($isPDF) && $isPDF)
    <form method="POST" action="{{ route('mcmc.reports.generate') }}">
        @csrf
        <div>
            <label>Date From:</label>
            <input type="date" name="from" value="{{ old('from') ?? request('from') }}">
            <label>Date To:</label>
            <input type="date" name="to" value="{{ old('to') ?? request('to') }}">
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
                <option value="Under Investigation" @if(request('status') == 'Under Investigation') selected @endif>Under Investigation</option>
                <option value="Verified as True" @if(request('status') == 'Verified as True') selected @endif>Verified as True</option>
                <option value="Identified as Fake" @if(request('status') == 'Identified as Fake') selected @endif>Identified as Fake</option>
                <option value="Rejected" @if(request('status') == 'Rejected') selected @endif>Rejected</option>
            </select>
        </div>

        <button type="submit">Generate Report</button>
    </form>
    @endunless

    @isset($report)
    <hr>
    <h3>Report Results</h3>

    {{-- Show date range info --}}
    @if(request('from') && request('to'))
        <p><strong>Date Range:</strong> {{ request('from') }} to {{ request('to') }}</p>
    @else
        <p><strong>Date Range:</strong> All records</p>
    @endif

    {{-- Overall Statistics Summary --}}
    @if(isset($overallStats))
    <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
        <h4>Overall Summary</h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div><strong>Total Assigned:</strong> {{ $overallStats['totalAssigned'] }}</div>
            <div><strong>Total Resolved:</strong> {{ $overallStats['totalResolved'] }}</div>
            <div><strong>Total Pending:</strong> {{ $overallStats['totalPending'] }}</div>
            <div><strong>Under Investigation:</strong> {{ $overallStats['totalUnderInvestigation'] }}</div>
        </div>
    </div>
    @endif

    {{-- Enhanced Performance Table --}}
    <table border="1" cellpadding="8" style="width: 100%; border-collapse: collapse;">
        <thead style="background-color: #f8f9fa;">
            <tr>
                <th>Agency</th>
                <th>Total Assigned</th>
                <th>Pending</th>
                <th>Under Investigation</th>
                <th>Resolved</th>
                <th>Resolution Rate (%)</th>
                <th>Avg. Resolution Time (days)</th>
                <th>Avg. Pending Delay (days)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report as $agency => $data)
            <tr>
                <td><strong>{{ $agency }}</strong></td>
                <td>{{ $data['assigned'] }}</td>
                <td>{{ $data['pending'] }}</td>
                <td>{{ $data['underInvestigation'] }}</td>
                <td>{{ $data['resolved'] }}</td>
                <td>{{ number_format($data['resolutionRate'], 1) }}%</td>
                <td>{{ number_format($data['avgResolutionTime']) }}</td>
                <td>{{ number_format($data['avgPendingDelay']) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Hide download buttons and chart in PDF --}}
    @unless(isset($isPDF) && $isPDF)
    <br>
    <div>
        <a href="{{ route('mcmc.reports.excel', request()->all()) }}" class="btn">Download Excel</a>
        <a href="{{ route('mcmc.reports.pdf', request()->all()) }}" class="btn">Download PDF</a>
    </div>

    <br>
    <canvas id="agencyChart" width="100" height="50"></canvas>
    @endunless

    @endisset

    {{-- Enhanced inquiry details with time information --}}
    @if(isset($rows) && $rows->count() && (!isset($isPDF) || !$isPDF))
    <hr>
    <h3>Inquiry Details</h3>
    <table border="1" cellpadding="8" style="width: 100%; border-collapse: collapse; font-size: 0.9em;">
        <thead style="background-color: #f8f9fa;">
            <tr>
                <th>Inquiry ID</th>
                <th>Agency</th>
                <th>Status</th>
                <th>Assign Date</th>
                <th>Investigation Begin Date</th>
                <th>Verification Date</th>
                <th>Resolution Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                @php
                    if (is_null($row->InvestigationBeginDate) && is_null($row->VerificationStatus)) {
                        $status = 'Pending';
                    } elseif ($row->InvestigationBeginDate && is_null($row->VerificationStatus)) {
                        $status = 'Under Investigation';
                    } else {
                        $status = $row->VerificationStatus;
                    }

                    $daysSinceAssignment = now()->diffInDays(\Carbon\Carbon::parse($row->AssignDate));
                    $resolutionTime = $row->VerificationDateTime ?
                        \Carbon\Carbon::parse($row->AssignDate)->diffInDays(\Carbon\Carbon::parse($row->VerificationDateTime)) : null;

                    $delayStatus = '';
                    if ($status === 'Pending' && $daysSinceAssignment > 7) {
                        $delayStatus = 'Overdue';
                    } elseif ($status === 'Under Investigation' && $row->InvestigationBeginDate) {
                        $daysInInvestigation = now()->diffInDays(\Carbon\Carbon::parse($row->InvestigationBeginDate));
                        if ($daysInInvestigation > 14) {
                            $delayStatus = 'Investigation Overdue';
                        }
                    }
                @endphp
                <tr>
                    <td>{{ $row->InquiryID }}</td>
                    <td>{{ $row->AgencyName }}</td>
                    <td>{{ $status }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->AssignDate)->format('Y-m-d') }}</td>
                    <td>
                        @if($row->InvestigationBeginDate)
                            {{ \Carbon\Carbon::parse($row->InvestigationBeginDate)->format('Y-m-d H:i:s') }}
                        @else
                            <span style="color: #6c757d;">Not Started</span>
                        @endif
                    </td>
                    <td>
                        @if($row->VerificationDateTime)
                            {{ \Carbon\Carbon::parse($row->VerificationDateTime)->format('Y-m-d H:i:s') }}
                        @else
                            <span style="color: #6c757d;">Not Completed</span>
                        @endif
                    </td>
                    <td>
                        @if($resolutionTime !== null)
                            {{ $resolutionTime }} days
                        @else
                            <span style="color: #6c757d;">In Progress</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

{{-- Hide JavaScript in PDF --}}
@unless(isset($isPDF) && $isPDF)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@if(isset($report))
<script>
    // Performance Chart
    const chartLabels = {!! json_encode($report->keys()) !!};
    const assigned = {!! json_encode($report->map->assigned->values()) !!};
    const resolved = {!! json_encode($report->map->resolved->values()) !!};
    const pending = {!! json_encode($report->map->pending->values()) !!};
    const underInvestigation = {!! json_encode($report->map->underInvestigation->values()) !!};

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
                },
                {
                    label: 'Under Investigation',
                    data: underInvestigation,
                    backgroundColor: '#fd7e14'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'Agency Inquiry Performance' }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endif
@endunless
@endsection
