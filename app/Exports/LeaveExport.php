<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LeaveExport implements WithMultipleSheets
{
    protected $year;

    public function __construct($year)
    {
        $this->year = $year;
    }

    public function sheets(): array
    {
        return [
            new LeaveSheetExport($this->year, 'leave', 'REKAP PENGGUNAAN CUTI - IZIN'),
            new LeaveSheetExport($this->year, 'replacer', 'REKAP PENGGANTI CUTI - IZIN'),
        ];
    }
}
