<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

//class ReportExport implements FromCollection, WithHeadings
{
    //protected $data;

    //public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    // public function collection()
    {
        return $this->data->map(function ($item) {
            return [
                $item->InquiryID,
                $item->InquiryTitle,
                \Carbon\Carbon::parse($item->SubmissionDate)->format('d M Y'),
                $item->AgencyName ?? 'Unassigned',
                ucfirst($item->SubmissionStatus),
            ];
        });
    }

    // public function headings(): array
    {
        return ['Inquiry ID', 'Title', 'Submitted On', 'Agency', 'Status'];
    }
}
