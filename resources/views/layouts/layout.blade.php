<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MySebenarnya</title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    @stack('styles')
    @yield('head')
</head>
<body>

<!-- Header -->
<div class="main-header"
     style="background:
     @if(session('user_role') === 'publicuser') #325c74 ;
     @elseif(session('user_role') === 'mcmc') rgb(104, 75, 142) ;
     @elseif(session('user_role') === 'agency') #a37e27 ;
     @endif color: white;">

    <div class="logo-container">
        <img src="{{ asset('images/logo.png') }}" alt="umpsa" class="umpsa">
        <img src="{{ asset('images/brand.png') }}" alt="brand" class="brand">
    </div>
    <span class="page-name">@yield('page-name', 'PageName')</span>
</div>

<!-- Sidebar -->
<div class="sidebar"
     style="background:
     @if(session('user_role') === 'publicuser') #325c74 ;
     @elseif(session('user_role') === 'mcmc') rgb(104, 75, 142) ;
     @elseif(session('user_role') === 'agency') #a37e27 ;
     @endif">
    <ul>
        @if(session('user_role') === 'publicuser')
            <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="#"><i class="fas fa-user"></i> User Profile</a></li>
            <li><a href="#"><i class="fas fa-comments"></i> Submit Inquiry</a></li>
            <li><a href="#"><i class="fas fa-newspaper"></i> Browse Verified News</a></li>

        @elseif(session('user_role') === 'mcmc')
            <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="#"><i class="fas fa-user"></i> User Profile</a></li>
            <li><a href="#"><i class="fas fa-user-plus"></i> Register Agency</a></li>
            <li><a href="#"><i class="fas fa-users"></i> View User Data</a></li>
            <li><a href="#"><i class="fas fa-chart-line"></i> Generate Reports</a></li>

        @elseif(session('user_role') === 'agency')
            <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="#"><i class="fas fa-user"></i> User Profile</a></li>
            <li><a href="{{ url('/agency/inquirylist') }}"><i class="fas fa-tasks"></i> Assigned Inquiry</a></li>
            <li><a href="#"><i class="fas fa-envelope"></i> Provide Feedback</a></li>
        @endif

        <li><a href="#"><i class="fas fa-history"></i> Activity Log</a></li>
        <li><a href="{{ route('logout') }}"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
    </ul>
</div>

<!-- Page Content -->
<section class="content">
    @yield('content') {{-- Page-specific content --}}
</section>

</body>
</html>

@stack('scripts')
