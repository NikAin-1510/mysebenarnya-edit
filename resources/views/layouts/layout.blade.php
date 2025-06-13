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
            height: 80px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 5px 0;
        }

        .logo {
            height: 65px;
            width: auto;
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .umpsa {
            height: 65px;
            width: auto;
            transition: transform 0.3s ease;
        }

        .umpsa:hover {
            transform: scale(1.05);
        }

        .brand {
            height: 50px;
            width: auto;
            margin-top: 5px;
            transition: transform 0.3s ease;
        }

        .brand:hover {
            transform: scale(1.05);
        }

        .page-name {
            font-size: 24px;
            font-weight: 600;
            color: white;
            white-space: nowrap;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 10px 20px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .page-name:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }
    </style>
    @stack('styles')
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
            <li><a href="{{ route('view.profile') }}"><i class="fas fa-user"></i> User Profile</a></li>
            <li><a href="#"><i class="fas fa-comments"></i> Submit Inquiry</a></li>
            <li><a href="#"><i class="fas fa-newspaper"></i> Browse Verified News</a></li>

        @elseif(session('user_role') === 'mcmc')
            <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="{{ route('view.profile') }}"><i class="fas fa-user"></i> User Profile</a></li>
            <li><a href="#"><i class="fas fa-user-plus"></i> Register Agency</a></li>
            <li><a href="#"><i class="fas fa-users"></i> View User Data</a></li>
            <li><a href="#"><i class="fas fa-chart-line"></i> Generate Reports</a></li>

        @elseif(session('user_role') === 'agency')
            <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="{{ route('view.profile') }}"><i class="fas fa-user"></i> User Profile</a></li>
            <li><a href="#"><i class="fas fa-tasks"></i> Track Cases</a></li>
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
