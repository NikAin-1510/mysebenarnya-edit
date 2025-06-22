<!DOCTYPE html>
<html>
<head>
    <title>Inquiry Assignment PDF Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px;}
        th, td { border: 1px solid #000; padding: 8px; text-align: left;}
    </style>
</head>
<body>
    <h2>Inquiry Assignment Report</h2>

    <table>
        <thead>
    <tr>
        <th>Assignment ID</th>
        <th>Agency Name</th>
        <th>Inquiry ID</th>
        <th>Assign Date</th>
        <th>Inquiry Title</th> <!-- change column header -->
    </tr>
</thead>
<tbody>
    @foreach ($assignments as $assignment)
        <tr>
            <td>{{ $assignment->AssignmentID }}</td>
            <td>{{ $assignment->agency->AgencyName ?? '' }}</td>
            <td>{{ $assignment->InquiryID }}</td>
            <td>{{ $assignment->AssignDate }}</td>
            <td>{{ $assignment->inquiry->InquiryTitle ?? '' }}</td> <!-- change data to InquiryTitle -->
        </tr>
    @endforeach
</tbody>
    </table>
</body>
</html>
