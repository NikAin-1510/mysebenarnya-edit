<!DOCTYPE html>
<html>
<head>
    <title>Inquiry Report PDF</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Inquiry Report</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Submitted</th>
                <th>Agency</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inquiries as $inquiry)
                <tr>
                    <td>{{ $inquiry->InquiryID }}</td>
                    <td>{{ $inquiry->InquiryTitle }}</td>
                    <td>{{ \Carbon\Carbon::parse($inquiry->SubmissionDate)->format('d M Y') }}</td>
                    <td>{{ $inquiry->AgencyName ?? 'Unassigned' }}</td>
                    <td>{{ ucfirst($inquiry->SubmissionStatus) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
