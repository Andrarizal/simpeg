<?php

namespace App\Exports;

use App\Models\ShiftExchange;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ShiftExchangeExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    protected $unitName;
    protected $period;

    public function __construct($unitName = '.....', $period = '.....')
    {
        $this->unitName = $unitName;
        $this->period = $period;
    }

    public function query()
    {
        return ShiftExchange::query()->whereMonth('exchange_date', Carbon::parse($this->period)->month)->whereYear('exchange_date', Carbon::parse($this->period)->year)->with([
            'staff', 
            'staffSchedule.shift', 
            'replacer', 
            'replacerSchedule.shift'
        ]);
    }

    public function headings(): array
    {
        return [
            ['REKAP TUKAR JADWAL UNIT ' . strtoupper($this->unitName) . ' PERIODE ' . strtoupper(Carbon::parse($this->period)->translatedFormat('F Y'))],
            ['Tanggal', 'Nama Penukar', 'Tukar Dengan', 'Status'],
        ];
    }

    public function map($record): array
    {
        $staffName = $record->staff?->name ?? '-';
        $staffShift = $record->staffSchedule?->shift?->name ?? '-';
        $replacerName = $record->replacer?->name ?? '-';
        $replacerShift = $record->replacerSchedule?->shift?->name ?? '-';

        return [
            Carbon::parse($record->exchange_date)->translatedFormat('d F Y'),
            "{$staffName} ({$staffShift})",
            "{$replacerName} ({$replacerShift})",
            $record->status,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->mergeCells('A1:D1');
                
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A2:D2')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
            },
        ];
    }
}
