<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MySebenarnya - First Time Login</title>
  <link rel="stylesheet" href="{{ asset('css/Module1/login.css') }}" />
</head>

<body>
  <div class="login-container">
    <div class="logo-container">
      <img src="{{ asset('images/logo.png') }}" alt="logo" class="logo">
      <img src="{{ asset('images/brand.png') }}" alt="brand" class="brand">
    </div>
    <br>
    
    <h2>Change Password Upon First Time Login</h2>

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

    @if($errors->any())
      <div class="alert alert-danger">
          <ul>
              @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('first.time.password.save') }}">
      @csrf

      <input type="password" name="new_password" placeholder="New Password" required />
      <input type="password" name="new_password_confirmation" placeholder="Confirm New Password" required />

      <button type="submit">Change Password</button>
    </form>
  </div>
</body>
</html>
