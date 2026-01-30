<?php

namespace App\Filament\Widgets;

use App\Models\Leave;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class LeavesOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 2;

    protected function getColumns(): int | array
    {
        return 2;
    }

    protected function getStats(): array
    {
        $staff = Auth::user()->staff;
        if (!$staff) return [];

        $year = now()->year;
        $maxLeave = setting('max_leave_days');
        $maxPermission = setting('max_permission_days');

        // Pro-rate jika pegawai baru masuk tahun ini
        if (Carbon::parse($staff->entry_date)->year == $year) {
            $monthJoin = Carbon::parse($staff->entry_date)->month;
            $maxLeave -= $monthJoin; // Asumsi: berkurang 1 hari per bulan yang lewat
            $maxPermission -= ceil($monthJoin / 2);
        }

        $leaves = Leave::where('staff_id', $staff->id)
                    ->whereYear('start_date', now()->year)
                    ->where(function ($query) {
                        $query->where('status', '!=', 'Ditolak')
                            ->orWhere('is_verified', '!=', 0)
                            ->orWhere('is_replaced', '!=', 0);
                    })
                    ->get(['type', 'subtype', 'start_date', 'end_date']);

        $usedLeave = $leaves->where('type', 'Cuti')
            ->where('subtype', 'Tahunan')
            ->sum(fn ($l) => Carbon::parse($l->start_date)->diffInDays(Carbon::parse($l->end_date)) + 1);

        $usedPermission = $leaves->where('type', 'Izin')
            ->where('subtype', 'Non-Sakit')
            ->sum(fn ($l) => Carbon::parse($l->start_date)->diffInDays(Carbon::parse($l->end_date)) + 1);

        return [
            Stat::make('Sisa Cuti Tahunan', max($maxLeave - $usedLeave, 0))
                ->description("Terpakai: {$usedLeave} dari {$maxLeave}")
                ->color($usedLeave > $maxLeave ? 'danger' : 'success'),

            Stat::make('Sisa Izin', max($maxPermission - $usedPermission, 0))
                ->description("Terpakai: {$usedPermission} dari {$maxPermission}")
                ->color('info'),
        ];
    }
}
