<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InquiryReportExport implements FromCollection, WithHeadings, WithStyles
{
    protected $data;

    public function __construct($inquiries)
    {
        $this->data = collect($inquiries)->map(function ($item) {
            return [
                $item->InquiryID,
                $item->InquiryTitle,
                $item->SubmissionCategory,
                $item->SubmissionStatus,
                $item->SubmissionDate,
                $item->AgencyName ?? 'Unassigned',
            ];
        });
    }

    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Inquiry ID',
            'Title',
            'Category',
            'Status',
            'Submission Date',
            'Agency Name',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
