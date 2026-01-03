<?php

namespace App\Filament\Widgets;

use App\Models\Staff;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SumStaffByUnit extends ChartWidget
{
    protected ?string $heading = 'Jumlah Pegawai berdasarkan Unit Kerja';
    protected static ?int $sort = 7;

    protected int | string | array $columnSpan = 2;

    protected function getData(): array
    {
        $staffByUnit = Staff::with('unit')
            ->select('unit_id', DB::raw('COUNT(*) as total'))
            ->groupBy('unit_id')
            ->get();

        $labels = $staffByUnit->pluck('unit.name')->toArray();
        $data = $staffByUnit->pluck('total')->toArray();
        $colors = $labels
        ? array_map(fn($label) => '#' . substr(md5($label), 0, 6), $labels)
        : [];

        return [
            'datasets' => [
                [
                    'label' => $labels,
                    'data' => $data, // data dari controller / query
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false, // Penting: Biarkan chart mengisi container tanpa terkunci rasio lama
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'display' => false, // Opsional: sembunyikan legend jika tidak perlu
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
                'x' => [
                    'ticks' => [
                        // Opsional: Miringkan text agar tidak tumpang tindih saat lebar berubah
                        'maxRotation' => 45,
                        'minRotation' => 45, 
                    ],
                ],
            ],
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()?->role_id == 1;
    }
}
