<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Complaint Form</title>
  <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

</head>
<body>s

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
