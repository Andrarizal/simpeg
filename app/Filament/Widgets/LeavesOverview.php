<?php

namespace App\Filament\Widgets;

use App\Models\Leave;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class LeavesOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $user = Auth::user();
        $user->staff_id = $user->staff_id ?? 1;
        $staff = $user->staff;
        
        // ambil max cuti dan izin dari table master dengan helper setting
        $maxLeave = setting('max_leave_days');
        $maxPermission = setting('max_permission_days');

        // cocokkan tahun masuk dengan tahun sekarang
        if (date('Y', strtotime($staff->entry_date)) === strval(now()->year)) {
            // kurangi sisa cuti dengan bulan yang sudah lewat
            $maxLeave -= date('m', strtotime($staff->entry_date));
            // kurangi sisa izin dengan bulan yang sudah lewat
            $maxPermission -= ceil(date('m', strtotime($staff->entry_date)) / 2);
        }

        // cek jumlah cuti yang pernah diambil dalam setahun
        $usedLeave = Leave::where('type', 'Cuti')
            ->where('subtype', 'Tahunan')
            ->where('staff_id', $staff->id)
            ->where('status', '!=', 'Ditolak')
            ->whereYear('start_date', now()->year)
            ->get()
            ->sum(function ($leave) {
                $start = Carbon::parse($leave->start_date);
                $end = Carbon::parse($leave->end_date);
                return $start->diffInDays($end);
            });

        // kurangi jumlah cuti dengan yang cuti sudah diambil
        $remainLeave = max($maxLeave - $usedLeave, 0);

        // ambil izin yang pernah disetujui
        $usedPermission = Leave::where('type', 'Izin')
            ->where('subtype', 'Non-Sakit')
            ->where('staff_id', $staff->id)
            ->where('status', '!=', 'Ditolak')
            ->whereYear('start_date', now()->year)
            ->get()
            ->sum(function ($leave) {
                $start = Carbon::parse($leave->start_date);
                $end = Carbon::parse($leave->end_date);
                return $start->diffInDays($end); // +1 agar termasuk hari pertama
            });
        
        // kurangi dengan izin yang pernah diambil
        $remainPermission = max($maxPermission - $usedPermission, 0);
        
        return [
            Stat::make('Sisa Cuti Tahunan Anda', $remainLeave),
            Stat::make('Total Cuti Tahunan '. date('Y'), $usedLeave),
            Stat::make('Sisa Izin Non-Sakit Anda', $remainPermission),
            Stat::make('Total Izin Non-Sakit '. date('Y'), $usedPermission),
        ];
    }
}
