<?php

namespace App\Exports;

use App\Models\InquiryAssignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InquiryAssignmentExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return InquiryAssignment::with('agency')
            ->get()
            ->map(function ($assignment) {
                return [
                    'AssignmentID' => $assignment->AssignmentID,
                    'AgencyName' => $assignment->agency->AgencyName ?? '',
                    'InquiryID' => $assignment->InquiryID,
                    'AssignDate' => $assignment->AssignDate,
                    'InquiryComment' => $assignment->InquiryComment,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Assignment ID',
            'Agency Name',
            'Inquiry ID',
            'Assign Date',
            'Inquiry Comment',
        ];
    }
}
