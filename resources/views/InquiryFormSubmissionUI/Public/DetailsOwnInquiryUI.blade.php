<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Public Dashboard</title>
  <link rel="stylesheet" href="{{ asset('css/Module3/public-dashboard.css') }}">
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
    <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
    <li><a href="#"><i class="fas fa-user"></i> User Profile</a></li>
    <li><a href="#"><i class="fas fa-comments"></i> Submit Inquiry</a></li>
    <li><a href="#"><i class="fas fa-newspaper"></i> Browse Verified News</a></li>
    <li><a href="#"><i class="fas fa-history"></i> Activity Log</a></li>
    <li><a href="#"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
  </ul>
</div>
<section class="content">
  <div class="dashboard-header">
    <h1><i class="fas fa-tachometer-alt"></i> Public User Dashboard</h1>
    <p>Track your news verification inquiries and view their current status</p>
  </div>
  <div class="stats-grid">
    @php
      $total = $inquiries->count();
      $forwarded = $inquiries->where('SubmissionStatus', 'forwarded')->count();
      $completed = $inquiries->where('SubmissionStatus', 'completed')->count();
      $pending = $inquiries->where('SubmissionStatus', 'pending')->count();
    @endphp
    <div class="stat-card"><div class="stat-number">{{ $total }}</div><div class="stat-label">Total Inquiries</div></div>
    <div class="stat-card"><div class="stat-number">{{ $forwarded }}</div><div class="stat-label">Forwarded to Agencies</div></div>
    <div class="stat-card"><div class="stat-number">{{ $completed }}</div><div class="stat-label">Completed</div></div>
    <div class="stat-card"><div class="stat-number">{{ $pending }}</div><div class="stat-label">Pending Review</div></div>
  </div>
  <div class="controls-section">
    <div class="search-container">
      <input type="text" class="search-box" id="searchBox" placeholder="Search inquiries...">
    </div>
    <div class="filter-buttons">
      <button class="filter-btn active" onclick="filterInquiries('all')"><i class="fas fa-list"></i> All Inquiries</button>
      <button class="filter-btn" onclick="filterInquiries('forwarded')"><i class="fas fa-share"></i> Forwarded</button>
      <button class="filter-btn" onclick="filterInquiries('pending')"><i class="fas fa-clock"></i> Pending</button>
      <button class="filter-btn" onclick="filterInquiries('completed')"><i class="fas fa-check-circle"></i> Completed</button>
    </div>
  </div>
  <div class="loading" id="loading">
    <div class="loading-spinner"></div>
    <p>Loading your inquiries...</p>
  </div>
  <div class="inquiries-section" id="inquiriesGrid">
    @foreach($inquiries as $inq)
      <div class="inquiry-card" data-status="{{ strtolower($inq->SubmissionStatus) }}">
        <div class="inquiry-header">
          <span class="inquiry-id">{{ $inq->InquiryID }}</span>
          <span class="inquiry-status status-{{ strtolower($inq->SubmissionStatus) }}">
            @if($inq->SubmissionStatus == 'forwarded') Forwarded to Agency
            @elseif($inq->SubmissionStatus == 'completed') Verification Complete
            @else Pending Assignment
            @endif
          </span>
        </div>
        <div class="inquiry-title">{{ $inq->InquiryTitle }}</div>
        <div class="inquiry-description">{{ $inq->InquiryDescription }}</div>
        <div class="agency-info">
          <div class="agency-header">
            <div class="agency-icon"><i class="fas fa-building"></i></div>
            <div class="agency-name">{{ $inq->AgencyName ?? 'Awaiting Agency Assignment' }}</div>
          </div>
          <div class="agency-details">
            @if($inq->InvestigationDetails)
              <strong>Verification Result:</strong> {{ $inq->InvestigationDetails }}
            @elseif($inq->AgencyName)
              Your inquiry has been forwarded to {{ $inq->AgencyName }}.
            @else
              Awaiting MCMC review for forwarding.
            @endif
          </div>
        </div>
        <div class="inquiry-dates">
          <div class="date-item"><i class="fas fa-calendar-plus"></i> Submitted: {{ date('d M Y', strtotime($inq->SubmissionDate)) }}</div>
          @if($inq->AssignDate)
            <div class="date-item"><i class="fas fa-share-square"></i> Forwarded: {{ date('d M Y', strtotime($inq->AssignDate)) }}</div>
          @endif
          @if($inq->VerificationDateTime)
            <div class="date-item"><i class="fas fa-check-circle"></i> Completed: {{ date('d M Y', strtotime($inq->VerificationDateTime)) }}</div>
          @endif
        </div>
        <div class="inquiry-actions">
    <a href="{{ route('inquiry.view', $inq->InquiryID) }}" class="action-btn view-btn"><i class="fas fa-eye"></i> View</a>
    <a href="{{ route('inquiry.edit', $inq->InquiryID) }}" class="action-btn edit-btn"><i class="fas fa-edit"></i> Edit</a>
    <form action="{{ route('inquiry.delete', $inq->InquiryID) }}" method="POST" class="inline-delete-form" onsubmit="return confirm('Are you sure you want to delete this inquiry?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="action-btn delete-btn"><i class="fas fa-trash"></i> Delete</button>
    </form>
</div>

      </div>
    @endforeach
  </div>
</section>
</body>
</html>
