<?php

namespace App\Exports;

use App\Models\Staff;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdministrationExport implements FromArray, ShouldAutoSize, WithStyles, WithEvents, WithColumnFormatting
{
    public function array(): array
    {
        $data = [];

        $data[] = ['REKAP ADMINISTRASI PEGAWAI RSU MITRA PARAMEDIKA', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '' ];
        $data[] = ['No', 'Nama', 'SIP', '', 'STR', '', 'SPK', '', 'RKK', '', 'MCU', '', 'UTW', '', 'Keterangan'];
        $data[] = ['', '', 'Upload', 'Tanggal Kadaluarsa', 'Upload', 'Tanggal Kadaluarsa', 'Upload', 'Tanggal Kadaluarsa', 'Upload', 'Tanggal Kadaluarsa', 'Upload', 'Tanggal Kadaluarsa', 'Upload', 'Tanggal Kadaluarsa', ''];
        $data[] = ['NAKES', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '' ];

        $currentRow = 5; 

        $nakesStaff = Staff::with('administration')
            ->whereNotIn('group_id', [1, 12])
            ->orderBy('unit_id')
            ->get();

        $no = 1;
        foreach ($nakesStaff as $staff) {
            $staffName = $staff->name ?? 'Pegawai Tidak Ditemukan';
            $administration = $staff->administration;

            $data[] = [
                $no++,
                $staffName,
                $administration->sip ? 'Sudah' : 'Belum',
                $administration->sip_expiry ? Date::dateTimeToExcel(Carbon::parse($administration->sip_expiry)) : '-',
                $administration->str ? 'Sudah' : 'Belum',
                $administration->str_expiry ? Date::dateTimeToExcel(Carbon::parse($administration->str_expiry)) : '-',
                $administration->spk ? 'Sudah' : 'Belum',
                $administration->spk_expiry ? Date::dateTimeToExcel(Carbon::parse($administration->spk_expiry)) : '-',
                $administration->rkk ? 'Sudah' : 'Belum',
                $administration->rkk_expiry ? Date::dateTimeToExcel(Carbon::parse($administration->rkk_expiry)) : '-',
                $administration->mcu ? 'Sudah' : 'Belum',
                $administration->mcu_expiry ? Date::dateTimeToExcel(Carbon::parse($administration->mcu_expiry)) : '-',
                $administration->utw ? 'Sudah' : 'Belum',
                $administration->utw_expiry ? Date::dateTimeToExcel(Carbon::parse($administration->utw_expiry)) : '-',
                $administration->is_verified ? 'Terverifikasi' : 'Belum Terverifikasi'
            ];
            $currentRow++;
        }

        $data[] = ['NON-NAKES', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '' ];

        $nonNakesStaff = Staff::with('administration')
            ->whereIn('group_id', [1, 12])
            ->orderBy('unit_id')
            ->get();

        $no = 1;
        foreach ($nonNakesStaff as $staff) {
            $staffName = $staff->name ?? 'Pegawai Tidak Ditemukan';
            $administration = $staff->administration;

            $data[] = [
                $no++,
                $staffName,
                '-',
                '-',
                '-',
                '-',
                '-',
                '-',
                '-',
                '-',
                $administration->mcu ? 'Sudah' : 'Belum',
                $administration->mcu_expiry ? Date::dateTimeToExcel(Carbon::parse($administration->mcu_expiry)) : '-',
                $administration->utw ? 'Sudah' : 'Belum',
                $administration->utw_expiry ? Date::dateTimeToExcel(Carbon::parse($administration->utw_expiry)) : '-',
                $administration->is_verified ? 'Terverifikasi' : 'Belum Terverifikasi'
            ];
            $currentRow++;
        }
        
        return $data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                $sheet->mergeCells('A1:O1'); 
                $sheet->mergeCells('A2:A3'); 
                $sheet->mergeCells('B2:B3'); 
                $sheet->mergeCells('C2:D2'); 
                $sheet->mergeCells('E2:F2'); 
                $sheet->mergeCells('G2:H2'); 
                $sheet->mergeCells('I2:J2'); 
                $sheet->mergeCells('K2:L2'); 
                $sheet->mergeCells('M2:N2'); 
                $sheet->mergeCells('O2:O3'); 

                $sheet->getStyle('A1:O3')->getFont()->setBold(true);
                $styleHeader = $sheet->getStyle('A1:O3')->getAlignment();
                $styleHeader->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $styleHeader->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->getStyle("C5:O{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }

    public function columnFormats(): array
    {
        $formatTanggal = '[$-id-ID]dd mmmm yyyy'; 

        return [
            'D'  => $formatTanggal, 
            'F'  => $formatTanggal, 
            'H'  => $formatTanggal, 
            'J'  => $formatTanggal, 
            'L'  => $formatTanggal, 
            'N'  => $formatTanggal, 
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
