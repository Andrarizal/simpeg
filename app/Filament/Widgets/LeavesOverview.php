<?php

namespace App\Filament\Widgets;

use App\Models\Leave;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class LeavesOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $staff = Auth::user()->staff;
        
        // ambil max cuti dan izindari table master dengan helper setting
        $maxLeave = setting('max_leave_days');
        $maxPermission = setting('max_permission_days');

        // cocokkan tahun masuk dengan tahun sekarang
        if (date('Y', strtotime($staff->work_entry_date)) === strval(now()->year)) {
            // kurangi sisa cuti dengan bulan yang sudah lewat
            $maxLeave -= date('m', strtotime($staff->work_entry_date));
        }

        // cek jumlah cuti yang pernah diambil dalam setahun
        $usedLeave = Leave::where('type', 'Cuti')
            ->where('staff_id', $staff->id)
            ->where('status', '!=', 'Ditolak')
            ->whereYear('start_date', now()->year)
            ->count();

        // kurangi jumlah cuti dengan yang cuti sudah diambil
        $remainLeave = max($maxLeave - $usedLeave, 0);

        // ambil izin yang pernah disetujui
        $usedPermission = Leave::where('type', 'Izin')
        ->where('staff_id', $staff->id)
        ->where('status', '!=', 'Ditolak')
        ->whereMonth('start_date', now()->month)
        ->count();
        
        // kurangi dengan izin yang pernah diambil
        $remainPermission = max($maxPermission - $usedPermission, 0);
        
        return [
            Stat::make('Sisa Cuti Anda', $remainLeave),
            Stat::make('Total Cuti Tahun ini', $usedLeave),
            Stat::make('Sisa Izin Anda', $remainPermission),
            Stat::make('Total Izin Bulan ini', $usedPermission),
        ];
    }

    public function getColumns(): int | array
    {
        return [
            'default' => 1,
            'md' => 2,
            'xl' => 2,
        ];
    }

    protected int|string|array $columnSpan = [
        'default' => 2,
        'md' => 1,
    ];
}
