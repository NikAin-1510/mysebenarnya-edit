@if(!session('user_role'))
    <script>window.location = "{{ route('login') }}";</script>
    {{-- or: use PHP redirect (less clean) --}}
    {{-- <?php header("Location: " . route('login')); exit; ?> --}}
@endif

@extends('layouts.layout')

@section('content')
    <div class="home-page">
        @if(session('user_role') === 'publicuser')
        <h2>Hello, Public User!</h2>
        <p>This is your homepage. Choose a menu from the left sidebar to begin.</p>

        @elseif(session('user_role') === 'mcmc')
        <h2>Hello, MCMC Staff!</h2>
        <p>This is your homepage. Choose a menu from the left sidebar to begin.</p>

        @elseif(session('user_role') === 'agency')
        <h2>Hello, Agency Staff!</h2>
        <p>This is your homepage. Choose a menu from the left sidebar to begin.</p>

        @endif
    </div>
@endsection
