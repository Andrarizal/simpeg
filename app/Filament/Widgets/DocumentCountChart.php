<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Letter;
use App\Models\Duty;
use Illuminate\Support\Facades\Auth;

class DocumentCountChart extends ChartWidget
{
    protected ?string $heading = 'Statistik Dokumen';

    protected int | string | array $columnSpan = 2;

    protected ?string $maxHeight = '180px';
    
    protected static ?int $sort = 5; 

    protected function getData(): array
    {
        $countDisposisi = Letter::where('classification', 'Disposisi')->whereYear('created_at', now()->year)->count();
        $countUndangan = Letter::where('classification', 'Undangan')->whereYear('created_at', now()->year)->count();
        $countSuratTugas = Duty::whereYear('created_at', now()->year)->count(); 

        return [
            'datasets' => [
                [
                    'label' => 'Total Disposisi',
                    'data' => [$countDisposisi, $countUndangan, $countSuratTugas],
                    'backgroundColor' => [
                        'rgba(54, 162, 235, 0.8)', 
                        'rgba(255, 206, 86, 0.8)', 
                        'rgba(75, 192, 192, 0.8)', 
                    ],
                    'borderColor' => [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                    ],
                    'borderColor' => '#ffffff', 
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Disposisi', 'Undangan', 'Surat Tugas'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public static function canView(): bool
    {
        return str_contains(Auth::user()?->staff->chair->name, 'Sekretariat');
    }
}