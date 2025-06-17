@extends('layouts.layout')

@section('page-name', 'Inquiry Form')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Module2/inquiryform.css') }}">
@endpush

@section('content')
<section class="content-header">
    <h2><i class="fas fa-tasks"></i> Inquiry Form</h2>
    <p>Fill in the form below to report your complaint with supporting evidence</p>
</section>

<section class="content-head2">
    <!-- ✅ START FORM -->
    <form method="POST" action="{{ route('inquiry.submit') }}" enctype="multipart/form-data">

        @csrf

        <!-- Title -->
        <div class="form-group">
            <label for="subject">Title:</label>
            <input type="text" id="subject" name="subject" placeholder="Enter inquiry subject" value="{{ old('subject') }}" required>
            @error('subject')
                <small class="error">{{ $message }}</small>
            @enderror
        </div>

        <!-- Description -->
        <div class="form-group">
            <label for="details">Inquiry Description:</label>
            <textarea id="details" name="details" placeholder="Describe your inquiry here..." required>{{ old('details') }}</textarea>
            @error('details')
                <small class="error">{{ $message }}</small>
            @enderror
        </div>

        <!-- URL Link -->
        <div class="form-group">
            <label for="type_link">URL Link:</label>
            <input type="url" id="type_link" name="type_link" placeholder="Paste a relevant URL (e.g., source, reference)" value="{{ old('type_link') }}" required>
            @error('type_link')
                <small class="error">{{ $message }}</small>
            @enderror
        </div>

        <!-- Evidence Upload -->
        <div class="form-group">
            <label for="evidence">Upload Evidence (Optional):</label>
            <input type="file" id="evidence" name="evidence" accept=".jpg,.jpeg,.png,.pdf,.docx,.mp4,.zip">
            <span class="file-note">Accepted formats: JPG, PNG, PDF, MP4, DOCX, ZIP. Max size: 5MB</span>
            @error('evidence')
                <small class="error">{{ $message }}</small>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="form-actions">
            <button type="submit" class="btn-submit">
                <i class="fas fa-paper-plane"></i> Submit Complaint
            </button>
        </div>
    </form>
    <!-- ✅ END FORM -->
</section>
@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#a37e27'
    });
</script>
@endif
@endpush
