


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agency Performance Report</title>

    @if(!isset($isPDF) || !$isPDF)
        <!-- CSS for web view -->
        <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
        <!-- Font Awesome Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    @else
        <!-- Inline CSS for PDF -->
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
                font-size: 12px;
                line-height: 1.4;
            }
            h2, h3, h4 {
                color: #333;
                margin-bottom: 10px;
            }
            h2 {
                text-align: center;
                border-bottom: 2px solid #333;
                padding-bottom: 10px;
                margin-bottom: 20px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f8f9fa;
                font-weight: bold;
            }
            .summary-box {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 5px;
                margin-bottom: 20px;
            }
            .text-center {
                text-align: center;
            }
            .summary-grid {
                display: table;
                width: 100%;
            }
            .summary-item {
                display: table-cell;
                width: 25%;
                padding: 5px;
            }
            .report-info {
                margin-bottom: 20px;
                padding: 10px;
                background: #e9ecef;
                border-left: 4px solid #007bff;
            }
            .page-break {
                page-break-before: always;
            }
        </style>
    @endif
</head>
<body>

@if(!isset($isPDF) || !$isPDF)
    <!-- Header for web view only -->
    <div class="main-header" style="background: @if(session('user_role') === 'mcmc') rgb(104, 75, 142) @endif;">
        <div class="logo-container">
            <img src="{{ asset('images/logo.png') }}" alt="umpsa" class="umpsa">
            <img src="{{ asset('images/brand.png') }}" alt="brand" class="brand">
        </div>
        <span class="page-name">Agency Performance Report</span>
    </div>

    <!-- Sidebar for web view only -->
    <div class="sidebar" style="background: @if(session('user_role') === 'mcmc') rgb(104, 75, 142) @endif;">
        <ul>
            <li><a href="{{ route('display.home') }}"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="{{ route('view.profile') }}"><i class="fas fa-user"></i> User Profile</a></li>
            <li><a href="{{ route('mcmc.all.inquiry') }}"><i class="fas fa-align-justify"></i> List Inquiry</a></li>
            <li><a href="{{ route('mcmc.new.inquiry') }}"><i class="far fa-clipboard"></i> New Inquiry</a></li>
            <li><a href="{{ route('show.register.agency') }}"><i class="fas fa-user-plus"></i> Register Agency</a></li>
            <li><a href="{{ route('view.all.users') }}"><i class="fas fa-users"></i> View All Users</a></li>
            <li><a href="{{ route('show.reportDashboard') }}"><i class="fas fa-chart-line"></i> Generate Reports</a></li>
            <li><a href="{{ route('logout') }}"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
        </ul>
    </div>

    <!-- Page Content with sidebar layout -->
    <section class="content">
@endif

    <!-- Main Content -->
    <div class="container">
        <h2>Agency Performance Report</h2>

        @if(!isset($isPDF) || !$isPDF)
            {{-- Form for web view only --}}
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
        @else
            {{-- PDF Report Info --}}
            <div class="report-info">
                @if(request('from') && request('to'))
                    <strong>Date Range:</strong> {{ request('from') }} to {{ request('to') }}<br>
                @else
                    <strong>Date Range:</strong> All records<br>
                @endif

                @if(request('agency'))
                    <strong>Filtered Agency:</strong>
                    @foreach($agencies as $a)
                        @if($a->AgencyID == request('agency'))
                            {{ $a->AgencyName }}
                        @endif
                    @endforeach
                    <br>
                @endif

                @if(request('status') && request('status') !== 'all')
                    <strong>Status Filter:</strong> {{ request('status') }}<br>
                @endif

                <strong>Generated:</strong> {{ now()->format('Y-m-d H:i:s') }}
            </div>
        @endif

        @isset($report)
            @if(!isset($isPDF) || !$isPDF)
                <hr>
            @endif
            @if(!isset($isPDF) || !$isPDF)
            <h3>Report Results</h3>
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
@endif

            {{-- Enhanced Performance Table --}}
            <table border="1" cellpadding="8" style="width: 100%; border-collapse: collapse;">
                <thead style="background-color: #f8f9fa;">
                    <tr>
                        <th>Agency</th>
                        <th class="@if(isset($isPDF) && $isPDF) text-center @endif">Total Assigned</th>
                        <th class="@if(isset($isPDF) && $isPDF) text-center @endif">Pending</th>
                        <th class="@if(isset($isPDF) && $isPDF) text-center @endif">Under Investigation</th>
                        <th class="@if(isset($isPDF) && $isPDF) text-center @endif">Resolved</th>
                        <th class="@if(isset($isPDF) && $isPDF) text-center @endif">Resolution Rate (%)</th>
                        <th class="@if(isset($isPDF) && $isPDF) text-center @endif">Avg. Resolution Time (days)</th>
                        <th class="@if(isset($isPDF) && $isPDF) text-center @endif">Avg. Pending Delay (days)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report as $agency => $data)
                    <tr>
                        <td><strong>{{ $agency }}</strong></td>
                        <td class="@if(isset($isPDF) && $isPDF) text-center @endif">{{ $data['assigned'] }}</td>
                        <td class="@if(isset($isPDF) && $isPDF) text-center @endif">{{ $data['pending'] }}</td>
                        <td class="@if(isset($isPDF) && $isPDF) text-center @endif">{{ $data['underInvestigation'] }}</td>
                        <td class="@if(isset($isPDF) && $isPDF) text-center @endif">{{ $data['resolved'] }}</td>
                        <td class="@if(isset($isPDF) && $isPDF) text-center @endif">{{ number_format($data['resolutionRate'], 1) }}%</td>
                        <td class="@if(isset($isPDF) && $isPDF) text-center @endif">{{ number_format($data['avgResolutionTime']) }}</td>
                        <td class="@if(isset($isPDF) && $isPDF) text-center @endif">{{ number_format($data['avgPendingDelay']) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if(!isset($isPDF) || !$isPDF)
                <br>
                <div>
                    <a href="{{ route('mcmc.reports.excel', request()->all()) }}" class="btn">Download Excel</a>
                    <a href="{{ route('mcmc.reports.pdf', request()->all()) }}" class="btn">Download PDF</a>
                </div>

                <br>
                <canvas id="agencyChart" width="100" height="50"></canvas>
            @endif
        @endisset

        {{-- Inquiry details --}}
        @if(isset($rows) && $rows->count())
            @if(isset($isPDF) && $isPDF)
                <div class="page-break">
                    <h3>Detailed Inquiry Information</h3>
            @else
                <hr>
                <h3>Inquiry Details</h3>
            @endif

            <table border="1" cellpadding="8" style="width: 100%; border-collapse: collapse; @if(!isset($isPDF) || !$isPDF) font-size: 0.9em; @endif">
                <thead style="background-color: #f8f9fa;">
                    <tr>
                        <th>Inquiry ID</th>
                        <th>Agency</th>
                        <th>Status</th>
                        <th>Assign Date</th>
                        <th>Investigation Begin @if(isset($isPDF) && $isPDF) @else Date @endif</th>
                        <th>Verification Date</th>
                        <th class="@if(isset($isPDF) && $isPDF) text-center @endif">Resolution Time</th>
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

                            $resolutionTime = $row->VerificationDateTime ?
                                (int) \Carbon\Carbon::parse($row->AssignDate)->diffInDays(\Carbon\Carbon::parse($row->VerificationDateTime)) : null;
                        @endphp
                        <tr>
                            <td>{{ $row->InquiryID }}</td>
                            <td>{{ $row->AgencyName }}</td>
                            <td>{{ $status }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->AssignDate)->format('Y-m-d') }}</td>
                            <td>
                                @if($row->InvestigationBeginDate)
                                    {{ \Carbon\Carbon::parse($row->InvestigationBeginDate)->format(isset($isPDF) && $isPDF ? 'Y-m-d' : 'Y-m-d H:i:s') }}
                                @else
                                    @if(isset($isPDF) && $isPDF)
                                        Not Started
                                    @else
                                        <span style="color: #6c757d;">Not Started</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($row->VerificationDateTime)
                                    {{ \Carbon\Carbon::parse($row->VerificationDateTime)->format(isset($isPDF) && $isPDF ? 'Y-m-d' : 'Y-m-d H:i:s') }}
                                @else
                                    @if(isset($isPDF) && $isPDF)
                                        Not Completed
                                    @else
                                        <span style="color: #6c757d;">Not Completed</span>
                                    @endif
                                @endif
                            </td>
                            <td class="@if(isset($isPDF) && $isPDF) text-center @endif">
                                @if($resolutionTime !== null)
                                    {{ $resolutionTime }} days
                                @else
                                    @if(isset($isPDF) && $isPDF)
                                        In Progress
                                    @else
                                        <span style="color: #6c757d;">In Progress</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if(isset($isPDF) && $isPDF)
                </div>
            @endif
        @endif

        @if(isset($isPDF) && $isPDF)
            {{-- PDF Footer --}}
            <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666;">
                Report generated on {{ now()->format('Y-m-d H:i:s') }}
            </div>
        @endif
    </div>

@if(!isset($isPDF) || !$isPDF)
    </section>

    {{-- JavaScript for web view only --}}
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
@endif

</body>
</html>
