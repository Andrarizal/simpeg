<?php

namespace App\Filament\Widgets;

use App\Models\Duty;
use App\Models\Letter;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class DocumentCompareChart extends ChartWidget
{
    protected ?string $heading = "Perbandingan Dokumen";

    protected int | string | array $columnSpan = 2;

    protected ?string $maxHeight = '180px';
    
    protected static ?int $sort = 6; 

    protected function getData(): array
    {
        $countDisposisi = Letter::where('classification', 'Disposisi')->whereYear('created_at', now()->year)->count();
        $countUndangan = Letter::where('classification', 'Undangan')->whereYear('created_at', now()->year)->count();
        $countSuratTugas = Duty::whereYear('created_at', now()->year)->count(); 

        return [
            'datasets' => [
                [
                    'label' => 'Total Dokumen',
                    'data' => [$countDisposisi, $countUndangan, $countSuratTugas],
                    'backgroundColor' => [
                        '#3b82f6', 
                        '#eab308', 
                        '#14b8a6', 
                    ],
                    'borderColor' => '#ffffff', 
                    'borderWidth' => 2,
                    'hoverOffset' => 4, 
                ],
            ],
            'labels' => ['Disposisi', 'Undangan', 'Surat Tugas'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut'; 
    }

    public static function canView(): bool
    {
        return str_contains(Auth::user()?->staff->chair->name, 'Sekretariat');
    }
}
