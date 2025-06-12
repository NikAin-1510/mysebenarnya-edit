<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MySebenarnya - Public User Dashboard</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>

    <style>
        @import url('https://fonts.googleapis.com/css?family=Roboto:300,400,400i,500');

        * {
            padding: 0;
            margin: 0;
            list-style: none;
            text-decoration: none;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f0f0;
        }

        /* Top Header */
        .main-header {
            position: fixed;
            top: 0;
            left: 0;
            height: 70px;
            width: 100%;
            display: flex;
            align-items: center;
            padding: 0 20px;
            z-index: 1000;
            background: #325c74;
            color: white;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo {
            height: 60px;
            width: auto;
        }

        .umpsa {
            height: 60px;
            width: 60px;
            background: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #325c74;
        }

        .brand {
            height: 45px;
            width: 120px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 10px;
            font-size: 12px;
            color: white;
        }

        .page-name {
            font-size: 22px;
            font-weight: 500;
            color: white;
            white-space: nowrap;
            align-self: center;
            text-align: center;
            margin: 0 auto;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 70px;
            left: 0;
            width: 250px;
            height: calc(100% - 70px);
            background: #325c74;
            transition: all 0.5s ease;
            overflow-y: auto;
        }

        .sidebar ul a {
            display: flex;
            align-items: center;
            height: 65px;
            width: 100%;
            font-size: 16px;
            color: white;
            padding-left: 20px;
            border-bottom: 1px solid #2a4f63;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            transition: 0.4s;
        }

        .sidebar ul li:hover a {
            padding-left: 35px;
            background-color: #ffffff1c;
        }

        .sidebar ul a i {
            margin-right: 16px;
            min-width: 20px;
            text-align: center;
        }

        /* Content Area */
        .content {
            margin-left: 250px;
            margin-top: 70px;
            padding: 20px;
            min-height: calc(100vh - 70px);
        }

        /* Dashboard Specific Styles */
        .dashboard-header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .dashboard-header h1 {
            color: #325c74;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .dashboard-header p {
            color: #666;
            font-size: 14px;
        }

        /* Search and Filter Section */
        .controls-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .search-container {
            margin-bottom: 15px;
        }

        .search-box {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .search-box:focus {
            outline: none;
            border-color: #325c74;
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 8px 16px;
            border: 2px solid #325c74;
            background: white;
            color: #325c74;
            border-radius: 20px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: #325c74;
            color: white;
        }

        /* Inquiry Cards */
        .inquiries-section {
            display: grid;
            gap: 20px;
        }

        .inquiry-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid #325c74;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .inquiry-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .inquiry-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .inquiry-id {
            background: #325c74;
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
        }

        .inquiry-status {
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-forwarded {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-completed {
            background: #cce7ff;
            color: #004085;
        }

        .inquiry-title {
            font-size: 18px;
            font-weight: 500;
            color: #333;
            margin-bottom: 10px;
        }

        .inquiry-description {
            color: #666;
            line-height: 1.5;
            margin-bottom: 15px;
            font-size: 14px;
        }

        /* Agency Information Box */
        .agency-info {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 15px;
            margin: 15px 0;
        }

        .agency-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .agency-icon {
            width: 35px;
            height: 35px;
            background: #325c74;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
        }

        .agency-name {
            font-weight: 500;
            color: #333;
            font-size: 15px;
        }

        .agency-details {
            color: #666;
            font-size: 13px;
            line-height: 1.4;
        }

        /* Inquiry Dates */
        .inquiry-dates {
            display: flex;
            gap: 20px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
            font-size: 13px;
            color: #666;
        }

        .date-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .date-item i {
            color: #325c74;
        }

        /* Loading Animation */
        .loading {
            display: none;
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .loading-spinner {
            width: 30px;
            height: 30px;
            border: 3px solid #e0e0e0;
            border-left: 3px solid #325c74;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-number {
            font-size: 24px;
            font-weight: 500;
            color: #325c74;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 13px;
            color: #666;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .empty-state i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 15px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .content {
                margin-left: 0;
            }

            .inquiry-dates {
                flex-direction: column;
                gap: 8px;
            }

            .filter-buttons {
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="main-header">
    <div class="logo-container">
        <div class="umpsa">UMPSA</div>
        <div class="brand">MCMC Brand</div>
    </div>
    <span class="page-name">MySebenarnya</span>
</div>

<!-- Sidebar -->
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

<!-- Page Content -->
<section class="content">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <h1><i class="fas fa-tachometer-alt"></i> Public User Dashboard</h1>
        <p>Track your news verification inquiries and view their current status</p>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">7</div>
            <div class="stat-label">Total Inquiries</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">3</div>
            <div class="stat-label">Forwarded to Agencies</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">2</div>
            <div class="stat-label">Completed</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">2</div>
            <div class="stat-label">Pending Review</div>
        </div>
    </div>

    <!-- Search and Filter Controls -->
    <div class="controls-section">
        <div class="search-container">
            <input type="text" class="search-box" placeholder="Search your inquiries by ID, title, or description..." id="searchBox">
        </div>

        <div class="filter-buttons">
            <button class="filter-btn active" onclick="filterInquiries('all')">
                <i class="fas fa-list"></i> All Inquiries
            </button>
            <button class="filter-btn" onclick="filterInquiries('forwarded')">
                <i class="fas fa-share"></i> Forwarded
            </button>
            <button class="filter-btn" onclick="filterInquiries('pending')">
                <i class="fas fa-clock"></i> Pending
            </button>
            <button class="filter-btn" onclick="filterInquiries('completed')">
                <i class="fas fa-check-circle"></i> Completed
            </button>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div class="loading" id="loading">
        <div class="loading-spinner"></div>
        <p>Loading your inquiries...</p>
    </div>

    <!-- Inquiries Section -->
    <div class="inquiries-section" id="inquiriesGrid">

        <!-- Inquiry Card 1 -->
        <div class="inquiry-card" data-status="forwarded">
            <div class="inquiry-header">
                <span class="inquiry-id">INQ-2024-001</span>
                <span class="inquiry-status status-forwarded">Forwarded to Agency</span>
            </div>

            <div class="inquiry-title">Health News Verification Request</div>
            <div class="inquiry-description">
                I received a WhatsApp message claiming that a new COVID-19 variant is spreading rapidly in Malaysia.
                The message includes unverified statistics and recommends specific home remedies. I need verification
                if this information is accurate and from official health authorities.
            </div>

            <div class="agency-info">
                <div class="agency-header">
                    <div class="agency-icon">
                        <i class="fas fa-hospital"></i>
                    </div>
                    <div class="agency-name">Ministry of Health Malaysia (MOH)</div>
                </div>
                <div class="agency-details">
                    Your inquiry has been successfully forwarded to the Ministry of Health Malaysia for verification
                    and fact-checking. The health department will review the claims and provide official clarification.
                </div>
            </div>

            <div class="inquiry-dates">
                <div class="date-item">
                    <i class="fas fa-calendar-plus"></i>
                    <span>Submitted: 15 Nov 2024</span>
                </div>
                <div class="date-item">
                    <i class="fas fa-share-square"></i>
                    <span>Forwarded: 16 Nov 2024</span>
                </div>
            </div>
        </div>

        <!-- Inquiry Card 2 -->
        <div class="inquiry-card" data-status="completed">
            <div class="inquiry-header">
                <span class="inquiry-id">INQ-2024-002</span>
                <span class="inquiry-status status-completed">Verification Complete</span>
            </div>

            <div class="inquiry-title">Political Policy Information Check</div>
            <div class="inquiry-description">
                Received a news article about new government policies regarding digital taxation for e-commerce businesses.
                The article contains information that conflicts with recent official government announcements.
            </div>

            <div class="agency-info">
                <div class="agency-header">
                    <div class="agency-icon">
                        <i class="fas fa-landmark"></i>
                    </div>
                    <div class="agency-name">Prime Minister's Department</div>
                </div>
                <div class="agency-details">
                    <strong>Verification Result:</strong> The article contains outdated information from the previous policy draft.
                    Official clarification has been published on the government portal with the correct details.
                </div>
            </div>

            <div class="inquiry-dates">
                <div class="date-item">
                    <i class="fas fa-calendar-plus"></i>
                    <span>Submitted: 10 Nov 2024</span>
                </div>
                <div class="date-item">
                    <i class="fas fa-share-square"></i>
                    <span>Forwarded: 11 Nov 2024</span>
                </div>
                <div class="date-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Completed: 14 Nov 2024</span>
                </div>
            </div>
        </div>

        <!-- Inquiry Card 3 -->
        <div class="inquiry-card" data-status="forwarded">
            <div class="inquiry-header">
                <span class="inquiry-id">INQ-2024-003</span>
                <span class="inquiry-status status-forwarded">Under Review</span>
            </div>

            <div class="inquiry-title">Economic Data Verification</div>
            <div class="inquiry-description">
                A viral social media post claims significant changes in Malaysia's GDP growth rate for Q3 2024.
                The figures mentioned don't align with Bank Negara Malaysia's recent official publications.
            </div>

            <div class="agency-info">
                <div class="agency-header">
                    <div class="agency-icon">
                        <i class="fas fa-university"></i>
                    </div>
                    <div class="agency-name">Bank Negara Malaysia</div>
                </div>
                <div class="agency-details">
                    Your inquiry is currently under review by Bank Negara Malaysia's Economics Department.
                    They are verifying the data accuracy and will provide official economic statistics.
                </div>
            </div>

            <div class="inquiry-dates">
                <div class="date-item">
                    <i class="fas fa-calendar-plus"></i>
                    <span>Submitted: 18 Nov 2024</span>
                </div>
                <div class="date-item">
                    <i class="fas fa-share-square"></i>
                    <span>Forwarded: 19 Nov 2024</span>
                </div>
            </div>
        </div>

        <!-- Inquiry Card 4 -->
        <div class="inquiry-card" data-status="pending">
            <div class="inquiry-header">
                <span class="inquiry-id">INQ-2024-004</span>
                <span class="inquiry-status status-pending">Pending Assignment</span>
            </div>

            <div class="inquiry-title">Education System Changes</div>
            <div class="inquiry-description">
                News circulating on social media about major changes to the Malaysian education curriculum and
                new examination formats for secondary schools. Need official verification from education authorities.
            </div>

            <div class="agency-info">
                <div class="agency-header">
                    <div class="agency-icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="agency-name">Awaiting Agency Assignment</div>
                </div>
                <div class="agency-details">
                    Your inquiry is currently being reviewed by MCMC administrators and will be forwarded to the
                    appropriate government agency (likely Ministry of Education) for verification shortly.
                </div>
            </div>

            <div class="inquiry-dates">
                <div class="date-item">
                    <i class="fas fa-calendar-plus"></i>
                    <span>Submitted: 20 Nov 2024</span>
                </div>
            </div>
        </div>

        <!-- Inquiry Card 5 -->
        <div class="inquiry-card" data-status="completed">
            <div class="inquiry-header">
                <span class="inquiry-id">INQ-2024-005</span>
                <span class="inquiry-status status-completed">Verification Complete</span>
            </div>

            <div class="inquiry-title">Transportation Policy Update</div>
            <div class="inquiry-description">
                Received information about new public transportation fare changes in Kuala Lumpur.
                The message claims significant price increases starting next month.
            </div>

            <div class="agency-info">
                <div class="agency-header">
                    <div class="agency-icon">
                        <i class="fas fa-bus"></i>
                    </div>
                    <div class="agency-name">Ministry of Transport Malaysia</div>
                </div>
                <div class="agency-details">
                    <strong>Verification Result:</strong> The information is partially accurate. Minor fare adjustments
                    will be implemented, but not as significant as claimed. Official announcement available on MOT website.
                </div>
            </div>

            <div class="inquiry-dates">
                <div class="date-item">
                    <i class="fas fa-calendar-plus"></i>
                    <span>Submitted: 08 Nov 2024</span>
                </div>
                <div class="date-item">
                    <i class="fas fa-share-square"></i>
                    <span>Forwarded: 09 Nov 2024</span>
                </div>
                <div class="date-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Completed: 12 Nov 2024</span>
                </div>
            </div>
        </div>

    </div>

</section>

<script>
    // Search functionality
    document.getElementById('searchBox').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const inquiryCards = document.querySelectorAll('.inquiry-card');

        inquiryCards.forEach(card => {
            const title = card.querySelector('.inquiry-title').textContent.toLowerCase();
            const description = card.querySelector('.inquiry-description').textContent.toLowerCase();
            const inquiryId = card.querySelector('.inquiry-id').textContent.toLowerCase();
            const agencyName = card.querySelector('.agency-name').textContent.toLowerCase();

            if (title.includes(searchTerm) ||
                description.includes(searchTerm) ||
                inquiryId.includes(searchTerm) ||
                agencyName.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });

        updateStats();
    });

    // Filter functionality
    function filterInquiries(status) {
        const inquiryCards = document.querySelectorAll('.inquiry-card');
        const filterButtons = document.querySelectorAll('.filter-btn');

        // Update active button
        filterButtons.forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');

        // Show loading
        document.getElementById('loading').style.display = 'block';
        document.getElementById('inquiriesGrid').style.opacity = '0.5';

        setTimeout(() => {
            inquiryCards.forEach(card => {
                if (status === 'all' || card.dataset.status === status) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });

            // Hide loading
            document.getElementById('loading').style.display = 'none';
            document.getElementById('inquiriesGrid').style.opacity = '1';

            updateStats();
        }, 500);
    }

    // Update statistics
    function updateStats() {
        const visibleCards = document.querySelectorAll('.inquiry-card[style*="block"], .inquiry-card:not([style*="none"])');
        const forwardedCount = Array.from(visibleCards).filter(card =>
            card.dataset.status === 'forwarded').length;
        const completedCount = Array.from(visibleCards).filter(card =>
            card.dataset.status === 'completed').length;
        const pendingCount = Array.from(visibleCards).filter(card =>
            card.dataset.status === 'pending').length;

        // Update stat cards if filtering
        const currentFilter = document.querySelector('.filter-btn.active').textContent.trim();
        if (currentFilter !== 'All Inquiries') {
            // Only update total when filtering
            document.querySelector('.stat-number').textContent = visibleCards.length;
        }
    }

    // Initialize page
    window.addEventListener('load', function() {
        const inquiryCards = document.querySelectorAll('.inquiry-card');
        inquiryCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'all 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });

    // Auto-refresh simulation
    setInterval(() => {
        console.log('Checking for inquiry updates...');
        // This would typically make an AJAX call to refresh data
    }, 30000);
</script>

</body>
</html>
