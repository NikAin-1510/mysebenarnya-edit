@extends('layouts.layout')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module2/inquiryform.css') }}">
@endpush
  <title>Complaint Form</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <style>
    * {
      font-family: 'Poppins', sans-serif;
      box-sizing: border-box;
    }

    body {
      background-color: #f8f8fc;
      padding: 30px;
    }

    .form-container {
      background: white;
      padding: 25px;
      max-width: 500px;
      margin: 0 auto;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .form-container h2 {
      color: #6B3FA0;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    label {
      font-weight: 500;
      margin-top: 15px;
      display: block;
      color: #333;
    }

    select,
    textarea,
    input[type="text"],
    input[type="url"],
    input[type="file"] {
      width: 100%;
      padding: 12px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 15px;
      transition: border-color 0.3s;
    }

    input:focus,
    textarea:focus {
      border-color: #6B3FA0;
      outline: none;
    }

    textarea {
      resize: vertical;
      min-height: 100px;
    }

    button {
      background-color: #6B3FA0;
      color: white;
      padding: 12px 25px;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      margin-top: 20px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    button:hover {
      background-color: #5a2e87;
    }
  </style>
</head>
<body>

<div class="form-container">
  <h2>📝 Submit a Complaint</h2>

  <form action="/submitComplaint" method="POST" enctype="multipart/form-data">
    <label for="subject">Complaint Subject:</label>
    <input type="text" id="subject" name="subject" placeholder="Enter complaint subject" required>

    <label for="typeLink">Complaint Type (URL):</label>
    <input type="url" id="typeLink" name="type_link" placeholder="Paste a link related to the complaint type" required>

    <label for="details">Complaint Details:</label>
    <textarea id="details" name="details" placeholder="Describe your complaint here..." required></textarea>

    <label for="evidence">Evidence (Upload File):</label>
    <input type="file" id="evidence" name="evidence" accept=".jpg,.jpeg,.png,.pdf,.docx,.mp4,.zip">

    <button type="submit">📨 Submit Complaint</button>
  </form>
</div>

</body>
</html>
