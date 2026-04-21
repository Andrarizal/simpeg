<?php

namespace App\Exports;

use App\Models\Letter;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DispositionExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    protected $year;
    private $rowNumber = 0;

    public function __construct($year)
    {
        $this->year = $year;
    }

    public function query()
    {
        return Letter::query()
            ->where('classification', 'Disposisi')
            ->whereYear('created_at', $this->year)
            ->with(['targetStaffs'])
            ->latest();
    }

    public function headings(): array
    {
        return [
            ['RESUME DISPOSISI RSU MITRA PARAMEDIKA TAHUN ' . $this->year],
            [
                'No',
                'Asal Surat',
                'Kepada',
                'Tanggal Surat',
                'Tanggal Diterima',
                'Nomor Surat',
                'Perihal Surat',
                'Agenda / Catatan',
            ],
        ];
    }

    public function map($record): array
    {
        $this->rowNumber++;

        $receiver = [];
        foreach($record->targetStaffs->unique(fn($s) => $s->chair->name ?? 'Staf') as $staff) {
            $receiver[] = $staff->chair->name;
        }
        if (empty($receiver)) {
            $receiver[] = 'Belum didistribusikan';
        }
        $receiver = implode(', ', $receiver);

        return [
            $this->rowNumber,
            $record->sender,
            $receiver,
            Carbon::parse($record->letter_date)->translatedFormat('d F Y'),
            Carbon::parse($record->created_at)->translatedFormat('d F Y'),
            $record->reference_number,
            $record->title,
            Carbon::parse($record->start_date)->translatedFormat('d F Y'),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->mergeCells('A1:H1');

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A2:H2')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                $sheet->getRowDimension(1)->setRowHeight(30);
            },
        ];
    }
}
