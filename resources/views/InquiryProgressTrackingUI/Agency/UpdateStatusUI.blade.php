@extends('layouts.layout')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/module4/agency-inquiry.css') }}">
@endsection

@section('content')
<form action="{{ url('/agency/updatestatus/save') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="InquiryID" value="{{ $inquiryID }}">
    <input type="hidden" name="AgencyID" value="{{ $agency->AgencyID }}">

    <div class="a-update-status">
        <p>Update Status:</p>
        <label for="inquiry-status">Inquiry Status:</label>
        <select id="inquiry-status" name="VerificationStatus" required>
            <option value="">--Select--</option>
            <option value="Under Investigation" {{ $progress?->VerificationStatus == 'Under Investigation' ? 'selected' : '' }}>Under Investigation</option>
            <option value="Verified as True" {{ $progress?->VerificationStatus == 'Verified as True' ? 'selected' : '' }}>Verified as True</option>
            <option value="Identified as Fake" {{ $progress?->VerificationStatus == 'Identified as Fake' ? 'selected' : '' }}>Identified as Fake</option>
            <option value="Rejected" {{ $progress?->VerificationStatus == 'Rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
    </div>

    <div class="a-investigation-details">
        <p>Investigation Details</p>

        <label for="InvestigationDetails">Explanation:</label><br>
        <textarea id="InvestigationDetails" name="InvestigationDetails" rows="5" cols="50">{{ $progress?->InvestigationDetails }}</textarea><br><br>

        <label for="InvestigationDoc">Upload Document:</label>
        <input type="file" name="InvestigationDoc" id="InvestigationDoc"><br><br>

        <p><strong>Last Updated:</strong>
        @if($progress?->VerificationStatus == 'Under Investigation')
            {{ $progress?->InvestigationBeginDate ?? 'Not yet updated' }}
        @elseif(in_array($progress?->VerificationStatus, ['Verified as True', 'Identified as Fake', 'Rejected']))
            {{ $progress?->VerificationDateTime ?? 'Not yet updated' }}
        @else
            Not yet updated
        @endif
</p>
        <p><strong>Updated By:</strong> {{ Auth::user()->name }}</p>
    </div>

    <button type="submit">Submit</button>
</form>

@if(in_array($progress?->VerificationStatus, ['Verified as True', 'Identified as Fake', 'Rejected']))
    <form action="{{ url('/agency/notify-mcmc') }}" method="POST" style="margin-top: 20px;">
        @csrf
        <input type="hidden" name="InquiryID" value="{{ $inquiryID }}">
        <button type="submit">Notify MCMC</button>
    </form>
@endif

<form action="{{ url('/agency/request-reassignment') }}" method="POST" style="margin-top: 20px;">
    @csrf
    <input type="hidden" name="InquiryID" value="{{ $inquiryID }}">
    <button type="submit" {{ $progress?->ReassignmentRequested ? 'disabled' : '' }}>Request Reassignment</button>
</form>
@endsection
