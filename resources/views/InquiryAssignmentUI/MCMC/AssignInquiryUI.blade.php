<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Inquiry</title>
    <link rel="stylesheet" href="{{ asset('css/mcmc-dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
</head>
<body>
    <div class="main-header">
        <div class="logo-container">
        <img src="{{ asset('images/logo.png') }}" alt="umpsa" class="umpsa">
        <img src="{{ asset('images/brand.png') }}" alt="brand" class="brand">
    </div>
        <span class="page-name">MySebenarnya</span>
    </div>

    <div class="sidebar">
        <ul>
            <li><a href="{{ route('agency.review.inquiry') }}"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="{{ route('agency.assign.form') }}" class="active"><i class="fas fa-tasks"></i> Assign Inquiries</a></li>
            <li><a href="{{ route('agency.display.report') }}"><i class="fas fa-chart-line"></i> Generate Reports</a></li>
        </ul>
    </div>

    <section class="content">
        <div class="content-header">
            <h1>Assign Inquiries to Agencies</h1>
            <p>Review and assign public inquiries to relevant agencies for verification</p>
        </div>

        <div class="dashboard-cards">
            <div class="card">
                <h3><i class="fas fa-tasks"></i> Assign New Inquiry</h3>
                <form method="POST" action="{{ route('agency.assign.inquiry') }}">
                    @csrf
                    <div class="form-group">
                        <label for="inquiry_id">Select Inquiry:</label>
                        <select id="inquiry_id" name="inquiry_id" required>
                            <option value="">-- Select an Inquiry --</option>
                            @foreach($inquiries as $inq)
                                <option value="{{ $inq->InquiryID }}">{{ $inq->InquiryTitle }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="agency_id">Assign to Agency:</label>
                        <select id="agency_id" name="agency_id" required>
                            <option value="">-- Select Agency --</option>
                            @foreach($agencies as $ag)
                                <option value="{{ $ag->AgencyID }}">{{ $ag->AgencyName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="comments">Comments/Notes:</label>
                        <textarea id="comments" name="comments" placeholder="Add any additional notes or comments for the agency..."></textarea>
                    </div>

                    <button type="submit" class="btn">
                        <i class="fas fa-paper-plane"></i> Assign Inquiry
                    </button>
                </form>
            </div>

            <div class="card">
                <h3><i class="fas fa-list"></i> Assignment History</h3>
                <div class="assignment-history">
                    @foreach($assignments as $a)
                        <div class="assignment-item">
                            <div class="agency-name">{{ $a->agency->AgencyName ?? 'N/A' }}</div>
                            <div class="date">Assigned on: {{ \Carbon\Carbon::parse($a->AssignDate)->format('F d, Y') }}</div>
                            <div class="inquiry-title">{{ $a->inquiry->InquiryTitle ?? 'N/A' }}</div>
                            @if($a->InquiryComment)
                                <div style="color: #666; font-size: 14px; margin-top: 5px;"><strong>Notes:</strong> {{ $a->InquiryComment }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</body>
</html>
