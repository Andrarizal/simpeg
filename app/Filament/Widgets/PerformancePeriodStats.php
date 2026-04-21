<?php

namespace App\Filament\Widgets;

use App\Models\PerformancePeriod;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class PerformancePeriodStats extends StatsOverviewWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 3;

    public function getColumns(): int | array
    {
        return 1;
    }

    protected function getStats(): array
    {
        $periods = PerformancePeriod::orderBy('start_date', 'asc')->get();

        if ($periods->isEmpty()) {
            return [Stat::make('Periode Aktif', 'Tidak Ada')];
        }

        $latestPeriod = $periods->last();
        $currentScore = $latestPeriod->score;

        $historicalScores = $periods->pluck('score')->toArray();

        $start = Carbon::parse($latestPeriod->start_date);
        $end = Carbon::parse($latestPeriod->end_date);
        $periodDescription = $start->translatedFormat('F') . ' - ' . $end->translatedFormat('F Y');

        $color = match (true) {
            $currentScore >= 80 => 'info',
            $currentScore >= 70 => 'success',
            $currentScore >= 50 => 'warning',
            default => 'danger',
        };

        return [
            Stat::make('Rata-rata Kinerja', number_format($currentScore, 2))
                ->description("Periode: " . $periodDescription)
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($color)
                ->chart($historicalScores) 
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()?->role_id == 1;
    }
}
