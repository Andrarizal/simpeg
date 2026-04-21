<?php
namespace App\Filament\Widgets;

use App\Models\Staff;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SumStaffByStatus extends ChartWidget
{
    protected ?string $heading = 'Pegawai berdasarkan Status';
    protected static ?int $sort = 9;
    protected ?string $maxHeight = '115px';

    protected int | string | array $columnSpan = 2;

    protected function getData(): array
    {
        $staffByStatus = Staff::with('staffStatus')
            ->select('staff_status_id', DB::raw('COUNT(*) as total'))
            ->groupBy('staff_status_id')
            ->get();

        $labels = $staffByStatus->pluck('staffStatus.name')->toArray();
        $data = $staffByStatus->pluck('total')->toArray();
        $colors = $labels ? array_map(fn($label) => '#' . substr(md5($label), 0, 6), $labels) : [];

        return [
            'datasets' => [
                [
                    'label' => 'Total',
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
        return 'doughnut'; // Tipe diubah ke doughnut
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'display' => true, // Ditampilkan untuk doughnut
                    'position' => 'bottom',
                ],
            ],
            'cutout' => '65%', // Menyesuaikan ketebalan cincin doughnut
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()?->role_id == 1;
    }
}