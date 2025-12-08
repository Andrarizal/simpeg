<?php

namespace App\Filament\Widgets;

use App\Models\PerformancePeriod;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class PerformancePeriodStats extends StatsOverviewWidget
{
    protected static ?int $sort = 5;

    protected function getStats(): array
    {
        $period = PerformancePeriod::where('status', true)->latest()->first();

        if (!$period) {
            return [Stat::make('Periode Aktif', 'Tidak Ada')];
        }

        $avgScore = $period->score ?? 0;

        $color = match (true) {
            $avgScore >= 80 => 'info',
            $avgScore >= 70 => 'success',
            $avgScore >= 50 => 'warning',
            default => 'danger',
        };

        return [
            Stat::make('Rata-rata Kinerja Periode Ini', number_format($avgScore, 2))
                ->description("Periode: " . $period->year)
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($color)
                ->chart([$avgScore, 100]) // Grafik mini
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()?->role_id == 1;
    }
}
