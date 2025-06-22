<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AgencyReportExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Return the data as an array for Excel export
     */
    public function array(): array
    {
        $rows = [];

        // Add overall statistics first
        if (isset($this->data['overallStats'])) {
            $rows[] = ['OVERALL SUMMARY', '', '', '', '', '', ''];
            $rows[] = [
                'Total Assigned: ' . $this->data['overallStats']['totalAssigned'],
                'Total Resolved: ' . $this->data['overallStats']['totalResolved'],
                'Total Pending: ' . $this->data['overallStats']['totalPending'],
                'Under Investigation: ' . $this->data['overallStats']['totalUnderInvestigation'],
                '',
                '',
                ''
            ];
            $rows[] = ['', '', '', '', '', '', '']; // Empty row
            $rows[] = ['AGENCY PERFORMANCE', '', '', '', '', '', ''];
        }

        // Add agency data
        foreach ($this->data['report'] as $item) {
            $rows[] = [
                $item['agency'],
                $item['assigned'],
                $item['resolved'],
                $item['pending'],
                $item['underInvestigation'],
                $item['avgResolutionTime'], // Already integer
                $item['avgPendingDelay'], // Already integer
                number_format($item['resolutionRate'], 1) . '%'
            ];
        }

        return $rows;
    }

    /**
     * Define the headings for the Excel file
     */
    public function headings(): array
    {
        return [
            'Agency Name',
            'Total Assigned',
            'Resolved',
            'Pending',
            'Under Investigation',
            'Avg. Resolution Time (days)',
            'Avg. Pending Delay (days)',
            'Resolution Rate (%)'
        ];
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * Set the worksheet title
     */
    public function title(): string
    {
        return 'Agency Report';
    }
}
