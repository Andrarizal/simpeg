<?php

namespace App\Exports;

use App\Models\OnCall;
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

class OnCallExport implements FromArray, ShouldAutoSize, WithStyles, WithEvents, WithColumnFormatting
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

        $data[] = ['REKAP ON CALL PERIODE ' . strtoupper($this->periodName), '', '', '', '', '', ''];
        $data[] = ['No', 'Tanggal', 'Pemberi Perintah', 'Perintah', 'Jam Tugas', '', 'Jumlah Jam'];
        $data[] = ['', '', '', '', 'Masuk Tugas', 'Pulang Tugas', ''];

        $currentRow = 3; 

        $oncallByStaff = OnCall::with('staff')
            ->where('period_id', $this->periodId)
            ->orderBy('staff_id')
            ->orderBy('oncall_date')
            ->get()
            ->groupBy('staff_id');

        foreach ($oncallByStaff as $staffId => $oncall) {
            $staffName = $oncall->first()->staff->name ?? 'Pegawai Tidak Ditemukan';
            $totalJam = 0;

            // --- A. BARIS NAMA PEGAWAI ---
            $data[] = [strtoupper($staffName), '', '', '', '', '', ''];
            $currentRow++;
            $this->staffRows[] = $currentRow;

            // --- B. BARIS RINCIAN LEMBUR ---
            $no = 1;
            foreach ($oncall as $oncall) {
                $totalJam += $oncall->hours;
                $data[] = [
                    $no++,
                    Date::dateTimeToExcel(Carbon::parse($oncall->oncall_date)) ?? null,
                    $oncall->commander->name,
                    $oncall->command,
                    Carbon::parse($oncall->start_time)->format('H:i'),
                    $oncall->end_time ? Carbon::parse($oncall->end_time)->format('H:i') : '-',
                    $oncall->hours ?? '-'
                ];
                $currentRow++;
            }

            // --- C. BARIS TOTAL JAM ---
            $data[] = ['TOTAL JAM LEMBUR ', '', '', '', '', '', $totalJam ?? '-'];
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

                $sheet->mergeCells('A1:G1'); 
                $sheet->mergeCells('A2:A3'); 
                $sheet->mergeCells('B2:B3'); 
                $sheet->mergeCells('C2:C3'); 
                $sheet->mergeCells('D2:D3'); 
                $sheet->mergeCells('E2:F2'); 
                $sheet->mergeCells('G2:G3'); 

                $styleHeader = $sheet->getStyle('A1:G3')->getAlignment();
                $styleHeader->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $styleHeader->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->getStyle("B4:B{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E4:G{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                foreach ($this->staffRows as $row) {
                    $sheet->mergeCells("A{$row}:G{$row}"); 
                    $sheet->getStyle("A{$row}:G{$row}")->getFill()
                          ->setFillType(Fill::FILL_SOLID)
                          ->getStartColor()->setARGB('FFD1D5DB'); 
                    $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                }

                foreach ($this->totalRows as $row) {
                    $sheet->mergeCells("A{$row}:F{$row}");
                    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("A{$row}:G{$row}")->getFont()->setBold(true);
                    
                    $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("G{$row}")->getFill()
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
