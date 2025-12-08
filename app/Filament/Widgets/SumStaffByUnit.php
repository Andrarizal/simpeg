<?php

namespace App\Filament\Widgets;

use App\Models\Staff;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SumStaffByUnit extends ChartWidget
{
    protected ?string $heading = 'Jumlah Pegawai berdasarkan Unit Kerja';
    protected static ?int $sort = 4;

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

    public static function canView(): bool
    {
        return Auth::user()?->role_id == 1;
    }
}
