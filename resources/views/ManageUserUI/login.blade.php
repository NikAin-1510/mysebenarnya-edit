

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MySebenarnya Login</title>
  <link rel="stylesheet" href="{{ asset('css/Module1/login.css') }}" />
</head>

<body>
  <div class="login-container">
    <div class="logo-container">
      <img src="{{ asset('images/logo.png') }}" alt="logo" class="logo">
      <img src="{{ asset('images/brand.png') }}" alt="brand" class="brand">
    </div>
    <br>
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
    <form method="POST" action="{{ route('login.submit') }}">
      @csrf <!-- Laravel CSRF protection -->

      <input type="email" name="email" placeholder="Email" required />
      <input type="password" name="password" placeholder="Password" required />

      <select name="role" required>
        <option value="" selected>Select Role</option>
        <option value="publicuser">Public User</option>
        <option value="mcmc">MCMC Staff</option>
        <option value="agency">Agency</option>
      </select>

      <button type="submit">Log in</button>
      <p>Don't have an account? <a href="{{ route('public.register') }}">Register here</a></p>
    </form>
  </div>
</body>
</html>