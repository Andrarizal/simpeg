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

        $year = now()->year + 1;
        $maxLeave = setting('max_leave_days');
        $maxPermission = setting('max_permission_days');

        if (Carbon::parse($staff->entry_date)->year == $year) {
            $monthJoin = Carbon::parse($staff->entry_date)->month;
            $maxLeave -= $monthJoin;
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
            ->whereIn('subtype', ['Tahunan', 'Darurat'])
            ->sum(fn ($l) => Carbon::parse($l->start_date)->diffInDays(Carbon::parse($l->end_date)) + 1);

        $usedPermission = $leaves->where('type', 'Izin')
            ->where('subtype', 'Non-Sakit')
            ->sum(fn ($l) => Carbon::parse($l->start_date)->diffInDays(Carbon::parse($l->end_date)) + 1);

        $isPermanent = $staff->staffStatus->name == 'Tetap';

        $isContract = $staff->staffStatus->name == 'Kontrak' 
            && $staff->entry_date 
            && Carbon::parse($staff->entry_date)->diffInMonths(now()) >= 12;

        return [
            Stat::make('Sisa Cuti Tahunan', function () use ($maxLeave, $usedLeave, $isPermanent, $isContract) {
                if ($isPermanent || $isContract) {
                    return max($maxLeave - $usedLeave, 0);
                }
                return 'N/A';
                })
                ->description(function () use ($maxLeave, $usedLeave, $isPermanent, $isContract) {
                if ($isPermanent || $isContract) {
                    return "Terpakai: {$usedLeave} dari {$maxLeave}";
                }
                return 'N/A';
                })
                ->color($usedLeave > $maxLeave ? 'danger' : 'success'),

            Stat::make('Sisa Izin', max($maxPermission - $usedPermission, 0))
                ->description("Terpakai: {$usedPermission} dari {$maxPermission}")
                ->color('info'),
        ];
    }
}
