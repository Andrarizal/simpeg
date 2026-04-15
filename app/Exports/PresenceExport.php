<?php

namespace App\Exports;

use App\Models\Staff;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PresenceExport implements FromArray, ShouldAutoSize, WithEvents
{
    protected $startDate;
    protected $endDate;
    
    protected $dates = [];
    protected $months = [];

    public function __construct($startDate, $endDate)
    {
        $this->startDate = Carbon::parse($startDate);
        $this->endDate = Carbon::parse($endDate);

        $current = $this->startDate->copy();
        
        while ($current->lte($this->endDate)) {
            $dateString = $current->format('Y-m-d');
            $this->dates[] = $dateString;
            
            $monthName = $current->translatedFormat('F'); 
            
            if (!isset($this->months[$monthName])) {
                $this->months[$monthName] = 0;
            }
            $this->months[$monthName]++;
            
            $current->addDay();
        }
    }

    public function array(): array
    {
        $data = [];

        $head = ['REKAP PRESENSI SELURUH PEGAWAI PERIODE ' . strtoupper(Carbon::parse($this->startDate)->translatedFormat('F')) . ' - ' . strtoupper(Carbon::parse($this->endDate)->translatedFormat('F')), '', '', ''];
        foreach($this->dates as $date) {
            $head[] = '';
        }
        $data[] = $head;

        // ---------------------------------------------------------
        // 2. BARIS HEADER 1: Nama Bulan
        // ---------------------------------------------------------
        $header1 = ['No', 'Nama', '']; 
        foreach ($this->months as $monthName => $daysCount) {
            $header1[] = $monthName;
            for ($i = 1; $i < $daysCount; $i++) {
                $header1[] = '';
            }
        }
        $header1[] = 'Rata-Rata & Total';
        $header1[] = 'Jam Kerja Kontraktual';
        $header1[] = 'Jam Kerja Aktual';
        $data[] = $header1;

        // ---------------------------------------------------------
        // 3. BARIS HEADER 2: Tanggal Angka (21, 22, 23...)
        // ---------------------------------------------------------
        $header2 = ['', '', '']; 
        foreach ($this->dates as $date) {
            $header2[] = Carbon::parse($date)->format('j'); 
        }
        $header2[] = ''; 
        $header2[] = ''; 
        $header2[] = ''; 
        $data[] = $header2;

        // ---------------------------------------------------------
        // 4. SUSUN DATA PEGAWAI (4 Baris per Pegawai)
        // ---------------------------------------------------------
        $staffs = Staff::with([
            'presences' => function ($query) {
                $query->whereBetween('presence_date', [
                    Carbon::parse($this->startDate)->startOfDay(), 
                    Carbon::parse($this->endDate)->endOfDay()
                ]);
            },
            'schedule' => function ($query) {
                $query->whereBetween('schedule_date', [
                    Carbon::parse($this->startDate)->startOfDay(), 
                    Carbon::parse($this->endDate)->endOfDay()
                ]);
            }
        ])->orderBy('unit_id')->get();

        $formatSeconds = function($seconds) {
            if ($seconds == 0) return '-';
            $seconds = abs($seconds);
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds / 60) % 60);
            $secs = $seconds % 60;
            return sprintf("%s%02d:%02d:%02d", '+', $hours, $minutes, $secs);
        };

        $no = 1;
        foreach ($staffs as $staff) {
            $presences = $staff->presences->keyBy(function ($item) {
                return Carbon::parse($item->presence_date)->format('Y-m-d');
            });
            
            $schedules = $staff->schedule->keyBy(function ($item) {
                return Carbon::parse($item->schedule_date)->format('Y-m-d');
            });

            $rowMasuk = [$no, $staff->name, 'Jam Masuk'];
            $rowPulang = ['', '', 'Jam Pulang'];
            $rowSelisihMasuk = ['', '', 'Selisih Masuk'];
            $rowSelisihPulang = ['', '', 'Selisih Pulang'];

            $total_gap_detik_masuk = 0;
            $total_gap_detik_pulang = 0;

            $sum_detik_masuk = 0;
            $count_masuk = 0;
            $sum_detik_pulang = 0;
            $count_pulang = 0;

            $total_target_hours = 0;
            $total_real_hours = 0;

            foreach ($this->dates as $date) {
                $gap_masuk = '-';
                $gap_pulang = '-';
                $jam_masuk = '-';
                $jam_pulang = '-';

                $p = $presences->get($date);
                $jadwal = $schedules->get($date);

                if ($jadwal && $jadwal->shift) {
                    $shift = $jadwal->shift;
                    
                    if ($shift->start_time && $shift->end_time) {
                        $target_masuk = Carbon::parse($shift->start_time);
                        $target_pulang = Carbon::parse($shift->end_time);
                        
                        if ($p) {
                            if ($p->check_in) {
                                $jam_masuk = Carbon::parse($p->check_in)->format('H:i:s');
                                $real_masuk = Carbon::parse($p->check_in);
                                $sec_masuk = $target_masuk->diffInSeconds($real_masuk, false);
                                if ($real_masuk > $target_masuk){
                                    $gap_masuk = '+' . $target_masuk->diff($real_masuk)->format('%H:%I:%S');
                                    $total_gap_detik_masuk += $sec_masuk;
                                    }
                                $sum_detik_masuk += ($real_masuk->hour * 3600) + ($real_masuk->minute * 60) + $real_masuk->second;
                                $count_masuk++;
                            }

                            if ($p->check_out) {
                                $jam_pulang = Carbon::parse($p->check_out)->format('H:i:s');
                                $real_pulang = Carbon::parse($p->check_out);
                                $sec_pulang = $target_pulang->diffInSeconds($real_pulang, false);
                                if ($real_pulang < $target_pulang) {
                                    $gap_pulang = '+' . $target_pulang->diff($real_pulang)->format('%H:%I:%S');
                                    $total_gap_detik_pulang += $sec_pulang;
                                    }
                                $sum_detik_pulang += ($real_pulang->hour * 3600) + ($real_pulang->minute * 60) + $real_pulang->second;
                                $count_pulang++;
                            }
                            if (isset($real_masuk) && isset($real_pulang)) {
                                $total_real_hours += round(abs($real_pulang->diffInMinutes($real_masuk) / 60), 2);
                            }
                        }
                        $total_target_hours += round(abs($target_pulang->diffInMinutes($target_masuk) / 60), 2);
                    }
                }

                $rowMasuk[] = $jam_masuk;
                $rowPulang[] = $jam_pulang;
                $rowSelisihMasuk[] = $gap_masuk;
                $rowSelisihPulang[] = $gap_pulang;
            }

            $rata_masuk_format = '-';
            if ($count_masuk > 0) {
                $avg_detik = $sum_detik_masuk / $count_masuk;
                $rata_masuk_format = sprintf("%02d:%02d", floor($avg_detik / 3600), floor(($avg_detik / 60) % 60));
            }

            $rata_pulang_format = '-';
            if ($count_pulang > 0) {
                $avg_detik = $sum_detik_pulang / $count_pulang;
                $rata_pulang_format = sprintf("%02d:%02d", floor($avg_detik / 3600), floor(($avg_detik / 60) % 60));
            }

            $rowMasuk[] = $rata_masuk_format; 
            $rowPulang[] = $rata_pulang_format;
            $rowSelisihMasuk[] = $formatSeconds($total_gap_detik_masuk);
            $rowSelisihPulang[] = $formatSeconds($total_gap_detik_pulang); 

            $rowMasuk[] = $total_target_hours . ' Jam';
            $rowMasuk[] = $total_real_hours . ' Jam';

            $data[] = $rowMasuk;
            $data[] = $rowPulang;
            $data[] = $rowSelisihMasuk;
            $data[] = $rowSelisihPulang;

            $no++;
        }

        return $data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                
                $totalDates = count($this->dates);
                $lastColIndex = 3 + $totalDates + 1; 
                $totalLetter = Coordinate::stringFromColumnIndex($lastColIndex);
                $contractualLetter = Coordinate::stringFromColumnIndex($lastColIndex + 1);
                $actualLetter = Coordinate::stringFromColumnIndex($lastColIndex + 2);

                // ---------------------------------------------------------
                // 1. MERGE HEADER STATIS
                // ---------------------------------------------------------
                $sheet->mergeCells("A1:{$actualLetter}1"); 
                $sheet->mergeCells('A2:A3'); 
                $sheet->mergeCells('B2:B3'); 
                $sheet->mergeCells('C2:C3'); 
                $sheet->mergeCells("{$totalLetter}2:{$totalLetter}3");
                $sheet->mergeCells("{$contractualLetter}2:{$contractualLetter}3");
                $sheet->mergeCells("{$actualLetter}2:{$actualLetter}3");

                // ---------------------------------------------------------
                // 2. MERGE HEADER BULAN DINAMIS
                // ---------------------------------------------------------
                $colIndex = 4; 
                foreach ($this->months as $monthName => $daysCount) {
                    $startCol = Coordinate::stringFromColumnIndex($colIndex);
                    $endCol = Coordinate::stringFromColumnIndex($colIndex + $daysCount - 1);
                    
                    $sheet->mergeCells("{$startCol}2:{$endCol}2");
                    
                    $colIndex += $daysCount; 
                }

                // ---------------------------------------------------------
                // 3. MERGE BARIS PEGAWAI (Lompat 4 Baris)
                // ---------------------------------------------------------
                for ($row = 4; $row <= $highestRow; $row += 4) {
                    $sheet->mergeCells("A{$row}:A" . ($row + 3)); 
                    $sheet->mergeCells("B{$row}:B" . ($row + 3)); 
                    $sheet->mergeCells("{$contractualLetter}{$row}:{$contractualLetter}" . ($row + 3)); 
                    $sheet->mergeCells("{$actualLetter}{$row}:{$actualLetter}" . ($row + 3)); 
                }

                // ---------------------------------------------------------
                // 4. STYLING & BORDER
                // ---------------------------------------------------------
                $sheet->getStyle("A1:{$actualLetter}{$highestRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                
                $sheet->getStyle("A1:{$actualLetter}3")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A1:{$actualLetter}3")->getFont()->setBold(true);

                $sheet->getStyle("A4:A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); 
                $sheet->getStyle("C4:{$actualLetter}{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); 

                $sheet->getStyle("A2:{$actualLetter}{$highestRow}")->applyFromArray([
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
