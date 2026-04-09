<?php

namespace App\Exports;

use App\Models\Staff;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LeaveSheetExport implements FromArray, ShouldAutoSize, WithEvents, WithTitle
{
    protected $year;
    protected $relation;
    protected $sheetTitle;

    // Menerima 3 parameter: Tahun, Nama Relasi (leaves/replacer), dan Judul Tab Excel
    public function __construct($year, $relation, $sheetTitle)
    {
        $this->year = $year;
        $this->relation = $relation;
        $this->sheetTitle = $sheetTitle;
    }

    // Memberi nama pada Tab Sheet Excel di bagian bawah
    public function title(): string
    {
        return $this->sheetTitle;
    }

    public function array(): array
    {
        $data = [];

        // ---------------------------------------------------------
        // 1. BARIS HEADER
        // ---------------------------------------------------------
        $data[] = [$this->sheetTitle . ' TAHUN ' . strtoupper($this->year), '', '', '', '', '', '', '', '', '', '', '', '', '', '', '' ];

        $data[] = [
            'No', 'Nama', 'Jenis', 'Bulan', 
            '', '', '', '', '', '', '', '', '', '', '', 
            'Total' 
        ];

        $data[] = [
            '', '', '', 
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
            '' 
        ];

        // ---------------------------------------------------------
        // 2. AMBIL DATA DARI DATABASE
        // ---------------------------------------------------------
        $staffs = Staff::with([$this->relation => function ($query) {
            $query->whereYear('start_date', $this->year); 
        }])->orderBy('unit_id')->get();

        $leaveTotal = array_fill(1, 12, 0);
        $permissionTotal = array_fill(1, 12, 0);

        // ---------------------------------------------------------
        // 3. SUSUN BARIS DATA PEGAWAI
        // ---------------------------------------------------------
        $no = 1;
        foreach ($staffs as $staff) {
            
            $leave = array_fill(1, 12, 0);
            $permission = array_fill(1, 12, 0);

            $records = $staff->{$this->relation};

            foreach ($records as $record) {
                $month = (int) Carbon::parse($record->start_date)->format('n'); 

                $start = Carbon::parse($record->start_date);
                $end = Carbon::parse($record->end_date ?? $record->start_date); 
                
                $days = $start->diffInDays($end) + 1;

                if (stripos($record->type, 'Cuti') !== false && stripos($record->subtype, 'Tahunan') !== false) {
                    $leave[$month] += $days;
                } else if (stripos($record->type, 'Izin') !== false && stripos($record->subtype, 'Non-Sakit') !== false) {
                    $permission[$month] += $days;
                }
            }

            for ($i = 1; $i <= 12; $i++) {
                $leaveTotal[$i] += $leave[$i];
                $permissionTotal[$i] += $permission[$i];
            }

            // --- A. Baris Pertama (Cuti) ---
            $rowCuti = [ $no, $staff->name, 'Cuti' ];
            for ($i = 1; $i <= 12; $i++) {
                $rowCuti[] = $leave[$i] ?: '0';
            }
            $rowCuti[] = array_sum($leave) ?: '0';

            // --- B. Baris Kedua (Izin) ---
            $rowIzin = [ '', '', 'Izin' ]; 
            for ($i = 1; $i <= 12; $i++) {
                $rowIzin[] = $permission[$i] ?: '0';
            }
            $rowIzin[] = array_sum($permission) ?: '0';

            $data[] = $rowCuti;
            $data[] = $rowIzin;

            $no++;
        }

        $rowTotalLeave = ['TOTAL', '', 'Cuti'];
        for ($i = 1; $i <= 12; $i++) {
            $rowTotalLeave[] = $leaveTotal[$i] ?: '0'; 
        }
        $rowTotalLeave[] = array_sum($leaveTotal) ?: '0';

        $rowTotalPermission = ['', '', 'Izin']; 
        for ($i = 1; $i <= 12; $i++) {
            $rowTotalPermission[] = $permissionTotal[$i] ?: '0'; 
        }
        $rowTotalPermission[] = array_sum($permissionTotal) ?: '0';

        $data[] = $rowTotalLeave;
        $data[] = $rowTotalPermission;

        return $data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                $sheet->mergeCells('A1:P1'); 
                $sheet->mergeCells('A2:A3'); 
                $sheet->mergeCells('B2:B3'); 
                $sheet->mergeCells('C2:C3'); 
                $sheet->mergeCells('D2:O2'); 
                $sheet->mergeCells('P2:P3'); 

                for ($row = 4; $row <= $highestRow - 2; $row += 2) {
                    $sheet->mergeCells("A{$row}:A" . ($row + 1)); 
                    $sheet->mergeCells("B{$row}:B" . ($row + 1)); 
                }

                $startTotalRow = $highestRow - 1;
                $sheet->mergeCells("A{$startTotalRow}:B{$highestRow}");

                $sheet->getStyle("A1:P{$highestRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->getStyle('A1:P3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1:P3')->getFont()->setBold(true); 

                $sheet->getStyle("A4:A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); 
                $sheet->getStyle("C4:P{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); 

                $sheet->getStyle("A{$startTotalRow}:P{$highestRow}")->getFont()->setBold(true);
                $sheet->getStyle("A{$startTotalRow}:P{$highestRow}")->getFill()
                      ->setFillType(Fill::FILL_SOLID)
                      ->getStartColor()->setARGB('FFF3F4F6');

                $sheet->getStyle("A2:P{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);
            }
        ];
    }
}
