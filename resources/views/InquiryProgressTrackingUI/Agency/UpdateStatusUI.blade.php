@extends('layouts.layout')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/module4/agency-inquiry.css') }}">
@endsection

@section('page-name', 'Update Status')

@section('content')
<div class="container">
    <h2 style="margin-bottom: 25px;">Update Status</h2>

    <form action="{{ url('/agency/updatestatus/save') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="InquiryID" value="{{ $inquiryID }}">
        <input type="hidden" name="AgencyID" value="{{ $agency->AgencyID }}">

        <div class="status-card">
            <div class="status-field">
                <label for="VerificationStatus">Inquiry Status:</label>
                <select name="VerificationStatus" id="VerificationStatus" required>
                    <option value="">--Select Status--</option>
                    <option value="Under Investigation"
                        {{ $progress?->InvestigationBeginDate && !$progress?->VerificationStatus ? 'selected' : '' }}>
                        Under Investigation
                    </option>
                    <option value="Verified as True"
                        {{ $progress?->VerificationStatus == 'Verified as True' ? 'selected' : '' }}>
                        Verified as True
                    </option>
                    <option value="Identified as Fake"
                        {{ $progress?->VerificationStatus == 'Identified as Fake' ? 'selected' : '' }}>
                        Identified as Fake
                    </option>
                    <option value="Rejected"
                        {{ $progress?->VerificationStatus == 'Rejected' ? 'selected' : '' }}>
                        Rejected
                    </option>
                </select>
            </div>
        </div>

        <div class="investigation-card">
            <h3>Investigation Details</h3>

            <div class="field-group">
                <label for="InvestigationDetails">Explanation:</label>
                <textarea id="InvestigationDetails" name="InvestigationDetails" rows="5" cols="50">{{ $progress?->InvestigationDetails }}</textarea>
            </div>

            <div class="field-group">
                <label for="InvestigationDoc">Upload Document:</label>
                <input type="file" name="InvestigationDoc" id="InvestigationDoc">
            </div>

            <div class="info-group">
                <p><strong>Last Updated:</strong>
                @if($progress?->VerificationStatus == 'Under Investigation')
                    {{ $progress?->InvestigationBeginDate ?? 'Not yet updated' }}
                @elseif(in_array($progress?->VerificationStatus, ['Verified as True', 'Identified as Fake', 'Rejected']))
                    {{ $progress?->VerificationDateTime ?? 'Not yet updated' }}
                @else
                    Not yet updated
                @endif
                </p>
                <p><strong>Updated By:</strong>
                    @php
                        $user = DB::table('user')->where('UserID', $agency->UserID)->first();
                    @endphp
                        {{ $user->Name ?? 'Unknown User' }}
                </p>
            </div>
        </div>

        <div class="notification-card">
            <div class="notification-field">
                <label for="notify-option">Notify MCMC:</label>
                <select name="Notify" id="notify-option">
                    <option value="">--Select Notification--</option>
                    <option value="Further clarification needed" {{ $progress?->Notify == 'Further clarification needed' ? 'selected' : '' }}>Further clarification needed</option>
                    <option value="Inquiry is completed" {{ $progress?->Notify == 'Inquiry is completed' ? 'selected' : '' }}>Inquiry is completed</option>
                    <option value="Reassignment requested" {{ $progress?->Notify == 'Reassignment requested' ? 'selected' : '' }}>Reassignment requested</option>
                </select>
            </div>
        </div>

        <div class="submit-button">
            <button type="submit">Submit</button>
        </div>
    </form>
</div>
@endsection
