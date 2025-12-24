<?php

namespace App\Filament\Widgets;

use App\Models\Leave;
use App\Models\Presence;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class DailyAttendanceStats extends StatsOverviewWidget
{
    protected static ?int $sort = 5;

    protected ?string $pollingInterval = '10s';

    protected int | string | array $columnSpan = 1;

    protected function getColumns(): int 
    {
        return 2;
    }

    protected function getStats(): array
    {
        $today = Carbon::today();

        $hadirCount = Presence::whereDate('presence_date', $today)
            ->count();

        $cutiIzinCount = Leave::whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->count();

        return [
            Stat::make('Kehadiran Hari Ini', $hadirCount)
                ->icon('heroicon-m-user-group')
                ->description('Pegawai')
                ->color('success'),

            Stat::make('Sedang Cuti / Izin', $cutiIzinCount)
                ->icon('heroicon-m-document-text')
                ->description('Pegawai')
                ->color('warning'),
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()?->role_id == 1;
    }
}
