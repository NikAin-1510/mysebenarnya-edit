<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MySebenarnya Registration</title>
  <link rel="stylesheet" href="{{ asset('css/Module1/register.css') }}" />
</head>

<body>
  <div class="register-container">
    <div class="logo-container">
      <img src="{{ asset('images/logo.png') }}" alt="logo" class="logo">
      <img src="{{ asset('images/brand.png') }}" alt="brand" class="brand">
    </div>

    <h1>Register as Public User</h1>

    {{-- Show Validation Errors --}}
    @if ($errors->any())
      <div class="error">
        @foreach ($errors->all() as $error)
          <p>{{ $error }}</p>
        @endforeach
      </div>
    @endif

    {{-- Show Flash Message --}}
    @if (session('success'))
      <div class="success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('public.register.store') }}">
      @csrf

      <input type="text" name="Name" placeholder="Full Name" required value="{{ old('Name') }}" />
      <input type="email" name="Email" placeholder="Email Address" required value="{{ old('Email') }}" />
      <input type="password" name="Password" placeholder="Create Password" required />
      <input type="password" name="Password_confirmation" placeholder="Confirm Password" required />

      <input type="tel" name="PhoneNum" placeholder="Phone Number (optional)" value="{{ old('PhoneNum') }}" />

      <select name="Gender">
          <option value="">Select Gender (optional)</option>
          <option value="male" {{ old('Gender') == 'male' ? 'selected' : '' }}>Male</option>
          <option value="female" {{ old('Gender') == 'female' ? 'selected' : '' }}>Female</option>
      </select>

      <button type="submit">Register</button>
      <p>Already registered? <a href="{{ route('login') }}">Login here</a></p>
    </form>
  </div>
</body>
</html>
