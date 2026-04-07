<?php

namespace App\Exports;

use App\Models\Staff;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StaffExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents, WithColumnFormatting
{
    public function query()
    {
        return Staff::query()->with(['staffStatus', 'chair', 'group', 'unit', 'contract', 'adjustment', 'appointment', 'entryEducation', 'workEducation', 'workExperience']); 
    }

    public function headings(): array
    {
        return [
            [
                'No', 'Nama Lengkap', 'Nomor Induk Kependudukan', 'Nomor Induk Pegawai', 'Tempat Lahir', 
                'Tanggal Lahir', 'Umur', 'TMT Kerja', 'Masa Kerja', 'Tanggal Pensiun', 
                'Status Kepegawaian', 'Kelompok Tenaga Kerja', 'Jabatan', 'Jenis Kelamin', 
                'Status Perkawinan', 'Nomor Handphone', 'Alamat Asli', 'Alamat Domisili', 'Email', 
                "Kontak Keluarga \n(Suami/Istri/Orang Tua)", 
                'Saat Masuk', '', '', '', '', '', 
                'Saat Bekerja', '', '', '', '',       
                'Pengalaman Kerja', '', '',           
                'Masa Berlaku Kontrak Kerja', '', '', '', 
                'Pengangkatan Pegawai Tetap', '', '', 
                'Penyesuaian Golongan Pegawai Tetap', '', '', 
            ],
            [
                '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 
                'Pendidikan Terakhir', 'Institusi', 'No Ijazah', 'Tanggal Ijazah', 'Pendidikan Non Formal', 'Keterangan',
                'Jenjang', 'Program Studi', 'Institusi Pendidikan', 'No Ijazah', 'Tanggal Ijazah',
                'Institusi Sebelum Bekerja di RS Mitra Paramedika', 'Lama Kerja di Institusi Lama', 'Pengakuan Pengalaman Kerja',
                'Nomor Kontrak', 'Tanggal Mulai', 'Tanggal Berakhir', 'Masa Berakhir Kontrak (Dalam Hari)',
                'Nomor SK', 'Tanggal Pengangkatan', 'Golongan Pertama',
                'SK Yayasan Kenaikan Golongan', 'Tanggal Penyesuaian Golongan', 'Golongan Saat Ini'
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $singleColumns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T'];
                foreach ($singleColumns as $col) {
                    $sheet->mergeCells("{$col}1:{$col}2");
                }

                $sheet->mergeCells('U1:Z1');
                $sheet->mergeCells('AA1:AE1');
                $sheet->mergeCells('AF1:AH1');
                $sheet->mergeCells('AI1:AL1');
                $sheet->mergeCells('AM1:AO1');
                $sheet->mergeCells('AP1:AR1');

                $style = $sheet->getStyle('A1:AR2')->getAlignment();
                $style->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $style->setVertical(Alignment::VERTICAL_CENTER);
                
                $style->setWrapText(true);
            },
        ];
    }

    public function map($staff): array
    {
        static $row = 0;
        $row++;

        // Kalkulasi Umur
        $age = $staff->birth_date ? Carbon::parse($staff->birth_date)->age . ' Tahun' : '-';

        // Kalkulasi Masa Kerja
        $serviceYear = '-';
        if ($staff->entry_date) {
            $entry = Carbon::parse($staff->entry_date);
            $serviceYear = $entry->diff(now())->format('%y Tahun %m Bulan');
        }

        // Sisa Hari Kontrak
        $contractRemain = '-';
        if ($staff->contract && $staff->contract->end_date) {
            $endDate = Carbon::parse($staff->contract->end_date);
            $contractRemain = now()->diffInDays($endDate, false);
            $contractRemain = $contractRemain > 0 ? $contractRemain . ' Hari' : 'Berakhir';
        }

        // Kontak Keluarga (Nomor + Hubungan)
        $otherContact = $staff->other_phone ? $staff->other_phone . ' (' . $staff->other_phone_adverb . ')' : '-';

        return [
            $row,
            $staff->name,
            "'". $staff->nik,
            $staff->nip,
            $staff->birth_place,
            $staff->birth_date ? Date::dateTimeToExcel(Carbon::parse($staff->birth_date)) : null,
            $age,
            $staff->entry_date ? Date::dateTimeToExcel(Carbon::parse($staff->entry_date)) : null,
            $serviceYear,
            $staff->retirement_date ? Date::dateTimeToExcel(Carbon::parse($staff->retirement_date)) : null,
            $staff->staffStatus->name ?? '-',
            $staff->group->name ?? '-',
            $staff->chair->name ?? '-',
            $staff->sex == 'L' ? 'Laki-laki' : ($staff->sex == 'P' ? 'Perempuan' : $staff->sex),
            $staff->marital,
            $staff->phone,
            $staff->origin,
            $staff->domicile,
            $staff->email,
            $otherContact,

            // --- SAAT MASUK (entryEducation) ---
            $staff->entryEducation->level ?? '-', 
            $staff->entryEducation->institution ?? '-',
            $staff->entryEducation->certificate_number ?? '-',
            $staff->entryEducation->certificate_date ? Date::dateTimeToExcel(Carbon::parse($staff->entryEducation->certificate_date)) : null,
            $staff->entryEducation->nonformal_education ?? '-',
            $staff->entryEducation->adverb ?? '-',

            // --- SAAT BEKERJA (workEducation) ---
            $staff->workEducation?->level ?? '-',
            $staff->workEducation?->major ?? '-',
            $staff->workEducation?->institution ?? '-',
            $staff->workEducation?->certificate_number ?? '-',
            $staff->workEducation?->certificate_date ? Date::dateTimeToExcel(Carbon::parse($staff->workEducation?->certificate_date)) : null,

            // --- PENGALAMAN KERJA ---
            $staff->workExperience?->institution ?? '-', 
            $staff->workExperience?->work_length ?? '-',
            $staff->workExperience?->admission ?? '-',

            // --- MASA BERLAKU KONTRAK KERJA (contract) ---
            $staff->contract?->contract_number ?? '-',
            $staff->contract?->start_date ? Date::dateTimeToExcel(Carbon::parse($staff->contract?->start_date)) : null,
            $staff->contract?->end_date ? Date::dateTimeToExcel(Carbon::parse($staff->contract?->end_date)) : null,
            $contractRemain,

            // --- PENGANGKATAN PEGAWAI TETAP (appointment) ---
            $staff->appointment?->decree_number ?? '-',
            $staff->appointment?->decree_date ? Date::dateTimeToExcel(Carbon::parse($staff->appointment?->decree_date)) : null,
            $staff->appointment?->class ?? '-',

            // --- PENYESUAIAN GOLONGAN (adjustment) ---
            $staff->adjustment?->decree_number ?? '-',
            $staff->adjustment?->decree_date ? Date::dateTimeToExcel(Carbon::parse($staff->adjustment?->decree_date)) : null,
            $staff->adjustment?->class ?? '-',
        ];
    }

    public function columnFormats(): array
    {
        $formatTanggal = '[$-id-ID]dd mmmm yyyy'; 

        return [
            'F'  => $formatTanggal, 
            'H'  => $formatTanggal, 
            'J'  => $formatTanggal, 
            'X'  => $formatTanggal, 
            'AE' => $formatTanggal, 
            'AJ' => $formatTanggal, 
            'AK' => $formatTanggal, 
            'AN' => $formatTanggal, 
            'AQ' => $formatTanggal, 
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            2 => ['font' => ['bold' => true]],
        ];
    }
}
