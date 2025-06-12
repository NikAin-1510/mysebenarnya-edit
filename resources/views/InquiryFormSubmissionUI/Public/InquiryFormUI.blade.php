<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Public Dashboard</title>
  <link rel="stylesheet" href="{{ asset('css/public-dashboard.css') }}">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
</head>
<body>
@section('title', 'Submit Inquiry')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Submit a New Inquiry</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form class="complaint-form" method="POST" action="{{ route('complaint.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="title" class="form-label">Inquiry Title</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Inquiry Description</label>
            <textarea name="description" id="description" rows="5" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label for="attachment" class="form-label">Attachment (optional)</label>
            <input type="file" name="attachment" id="attachment" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Submit Inquiry</button>
    </form>
</div>
@endsection
</body></html>
