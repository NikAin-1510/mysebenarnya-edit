<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Logged Out - MySebenarnya</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

  <style>
    body {
      background: url('../images/loginbg.jpg') no-repeat center center fixed;
      margin: 0;
      padding: 0;
      font-family: 'Inter', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .logout-container {
      background-color: #D1E7FE;
      padding: 3rem 2rem;
      border-radius: 16px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      text-align: center;
      max-width: 400px;
    }

    h2 {
      color: #333;
      font-size: 1.8rem;
      margin-bottom: 1rem;
    }

    p {
      color: #666;
      font-size: 1rem;
      margin-bottom: 2rem;
    }

    .button {
      text-decoration: none;
      background-color: #007BFF;
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: 8px;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }

    .button:hover {
      background-color: #0056b3;
    }
  </style>
</head>

<body>
  <div class="logout-container">
    <h2>You have been logged out.</h2>
    <p>Thank you for using <strong>MySebenarnya</strong>. See you again soon!</p>

    <a href="{{ route('login') }}" class="button">🔐 Log in again</a>
  </div>
</body>
</html>
