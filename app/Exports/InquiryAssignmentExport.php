<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InquiryAssignmentExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $collection;

    public function __construct(array $data)
    {
        // Flatten array to collection
        $rows = [];

        if (isset($data['overallStats'])) {
            $rows[] = ['OVERALL SUMMARY', '', ''];
            $rows[] = [
                'Total Assigned: ' . $data['overallStats']['totalAssigned'],
                '',
                ''
            ];
            $rows[] = ['', '', ''];
            $rows[] = ['AGENCY PERFORMANCE', '', ''];
        }

        foreach ($data['report'] as $item) {
            $rows[] = [
                $item['agency'],
                $item['assigned']
            ];
        }

        $this->collection = collect($rows);
    }

    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        return [
            'Agency Name',
            'Total Assigned'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Inquiry Assignment Report';
    }
}
