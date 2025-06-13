@extends('layouts.layout')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module3/mcmc-dashboard.css') }}">
@endpush
    <div class="sidebar">
        <ul>
            <li><a href="{{ route('agency.review.inquiry') }}"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="{{ route('agency.assign.form') }}"><i class="fas fa-tasks"></i> Assign Inquiries</a></li>
            <li><a href="{{ route('agency.display.report') }}" class="active"><i class="fas fa-chart-line"></i> Generate Reports</a></li>
        </ul>
    </div>

    <section class="content">
        <div class="content-header">
            <h1>Inquiry Assignment Reports</h1>
            <p>Generate and analyze reports on inquiry distribution across agencies</p>
        </div>

        <div class="card">
            <h3><i class="fas fa-filter"></i> Report Filters</h3>
            <div class="filters">
                <div class="filter-group">
                    <label for="dateFrom">From Date:</label>
                    <input type="date" id="dateFrom" value="2025-01-01">
                </div>
                <div class="filter-group">
                    <label for="dateTo">To Date:</label>
                    <input type="date" id="dateTo" value="{{ now()->toDateString() }}">
                </div>
                <div class="filter-group">
                    <label for="agencyFilter">Agency:</label>
                    <select id="agencyFilter">
                        <option value="">All Agencies</option>
                        @foreach($agencies as $agency)
                            <option value="{{ $agency->AgencyName }}">{{ $agency->AgencyName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label>&nbsp;</label>
                    <button type="button" class="btn" onclick="generateReport()">
                        <i class="fas fa-chart-bar"></i> Generate Report
                    </button>
                </div>
            </div>
        </div>

        <div class="dashboard-cards">
            <div class="card">
                <h3><i class="fas fa-chart-pie"></i> Inquiry Distribution</h3>
                <div class="chart-container">
                    <canvas id="distributionChart"></canvas>
                </div>
            </div>
        </div>

        <div class="download-buttons">
            <button class="btn" onclick="downloadReport('pdf')">
                <i class="fas fa-file-pdf"></i> Download PDF
            </button>
            <button class="btn btn-secondary" onclick="downloadReport('excel')">
                <i class="fas fa-file-excel"></i> Download Excel
            </button>
        </div>
    </section>

    <script>
        function generateReport() {
            const from = document.getElementById('dateFrom').value;
            const to = document.getElementById('dateTo').value;
            const agency = document.getElementById('agencyFilter').value;

            fetch(`/api/reports?from=${from}&to=${to}&agency=${agency}`)
                .then(response => response.json())
                .then(data => {
                    const ctx = document.getElementById('distributionChart').getContext('2d');
                    if (window.distributionChart) window.distributionChart.destroy();

                    window.distributionChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                data: data.values,
                                backgroundColor: ['#684b8e', '#36A2EB', '#FFCE56'],
                                borderColor: '#fff',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { position: 'bottom' } }
                        }
                    });
                });
        }

        function downloadReport(format) {
            alert(format.toUpperCase() + ' report download started...');
        }
    </script>
</body>
</html>
