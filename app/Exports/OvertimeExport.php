<?php

namespace App\Exports;

use App\Models\Overtime;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OvertimeExport implements FromArray, ShouldAutoSize, WithStyles, WithEvents, WithColumnFormatting
{
    protected $periodId;
    protected $periodName;
    protected $staffRows = []; 
    protected $totalRows = [];

    public function __construct($periodId, $periodName)
    {
        $this->periodId = $periodId;
        $this->periodName = $periodName;
    }

    public function array(): array
    {
        $data = [];

        $data[] = ['REKAP LEMBUR PERIODE ' . strtoupper($this->periodName), '', '', '', '', ''];
        $data[] = ['No', 'Tanggal', 'Perintah', 'Jam Lembur', '', 'Jumlah Jam'];
        $data[] = ['', '', '', 'Masuk Lembur', 'Pulang Lembur', ''];

        $currentRow = 3; 

        $overtimesByStaff = Overtime::with('staff')
            ->where('period_id', $this->periodId)
            ->orderBy('staff_id')
            ->orderBy('overtime_date')
            ->get()
            ->groupBy('staff_id');

        foreach ($overtimesByStaff as $staffId => $overtimes) {
            $staffName = $overtimes->first()->staff->name ?? 'Pegawai Tidak Ditemukan';
            $totalJam = 0;

            // --- A. BARIS NAMA PEGAWAI ---
            $data[] = [strtoupper($staffName), '', '', '', '', ''];
            $currentRow++;
            $this->staffRows[] = $currentRow;

            // --- B. BARIS RINCIAN LEMBUR ---
            $no = 1;
            foreach ($overtimes as $overtime) {
                $totalJam += $overtime->hours;
                $data[] = [
                    $no++,
                    Date::dateTimeToExcel(Carbon::parse($overtime->overtime_date)) ?? null,
                    $overtime->command,
                    Carbon::parse($overtime->start_time)->format('H:i'),
                    $overtime->end_time ? Carbon::parse($overtime->end_time)->format('H:i') : '-',
                    $overtime->hours ?? '-'
                ];
                $currentRow++;
            }

            // --- C. BARIS TOTAL JAM ---
            $data[] = ['TOTAL JAM LEMBUR ', '', '', '', '', $totalJam ?? '-'];
            $currentRow++;
            $this->totalRows[] = $currentRow;
        }

        return $data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                $sheet->mergeCells('A1:F1'); 
                $sheet->mergeCells('A2:A3'); 
                $sheet->mergeCells('B2:B3'); 
                $sheet->mergeCells('C2:C3'); 
                $sheet->mergeCells('D2:E2'); 
                $sheet->mergeCells('F2:F3'); 

                $styleHeader = $sheet->getStyle('A1:F3')->getAlignment();
                $styleHeader->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $styleHeader->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->getStyle("B4:B{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D4:F{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                foreach ($this->staffRows as $row) {
                    $sheet->mergeCells("A{$row}:F{$row}"); 
                    $sheet->getStyle("A{$row}:F{$row}")->getFill()
                          ->setFillType(Fill::FILL_SOLID)
                          ->getStartColor()->setARGB('FFD1D5DB'); 
                    $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                }

                foreach ($this->totalRows as $row) {
                    $sheet->mergeCells("A{$row}:E{$row}");
                    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("A{$row}:F{$row}")->getFont()->setBold(true);
                    
                    $sheet->getStyle("F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("F{$row}")->getFill()
                          ->setFillType(Fill::FILL_SOLID)
                          ->getStartColor()->setARGB('FFF3F4F6'); 
                }
            }
        ];
    }

    public function columnFormats(): array
    {
        $formatTanggal = '[$-id-ID]dd mmmm yyyy'; 

        return [
            'B'  => $formatTanggal, 
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
        ];
    }
}
