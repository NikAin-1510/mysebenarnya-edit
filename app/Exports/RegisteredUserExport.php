<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RegisteredUserExport implements FromCollection, WithHeadings
{
    private Collection $users;

    public function __construct(Collection $users)
    {
        $this->users = $users;
    }

    public function collection(): Collection
    {
        return $this->users->map(fn ($u) => [
            $u->Name,
            ucfirst($u->Role),
            \Carbon\Carbon::parse($u->Created_At)->format('Y-m-d'),
        ]);
    }

    public function headings(): array
    {
        return ['Name', 'Role', 'Registration Date'];
    }
}
