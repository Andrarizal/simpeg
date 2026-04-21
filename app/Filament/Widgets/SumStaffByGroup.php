<?php
namespace App\Filament\Widgets;

use App\Models\Staff;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SumStaffByGroup extends ChartWidget
{
    protected ?string $heading = 'Pegawai berdasarkan Kelompok Tenaga Kerja';
    protected static ?int $sort = 10;

    protected ?string $maxHeight = '200px';

    protected int | string | array $columnSpan = 'full'; // Dibuat lebih lebar karena bar chart

    protected function getData(): array
    {
        // Pastikan nama relasi 'group' sesuai dengan Model Staff
        $staffByGroup = Staff::with('group')
            ->select('group_id', DB::raw('COUNT(*) as total'))
            ->groupBy('group_id')
            ->get();

        $labels = $staffByGroup->pluck('group.name')->toArray();
        $data = $staffByGroup->pluck('total')->toArray();
        $colors = $labels ? array_map(fn($label) => '#' . substr(md5($label), 0, 6), $labels) : [];

        return [
            'datasets' => [
                [
                    'label' => 'Total Pegawai', // Label dataset
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Tipe tetap bar
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'display' => false, 
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