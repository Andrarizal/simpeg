<?php

namespace App\Filament\Widgets;

use App\Models\Leave;
use App\Models\Presence;
use App\Models\Schedule;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class WeeklyPresence extends Widget
{
    protected static ?int $sort = 2;
    protected string $view = 'filament.widgets.weekly-presence';
    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $user = Auth::user();
        $staffId = $user->staff_id ?? 0; // Sesuaikan relasi user ke staff

        // 1. Tentukan Range Tanggal (Senin - Minggu)
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $today = Carbon::now()->startOfDay();

        // 2. Ambil DATA PRESENSI seminggu sekaligus (KeyBy date agar mudah diakses)
        $presences = Presence::where('staff_id', $staffId)
            ->whereBetween('presence_date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
            ->get()
            ->keyBy(fn($item) => Carbon::parse($item->presence_date)->format('Y-m-d'));

        // 3. Ambil DATA JADWAL seminggu sekaligus
        // Asumsi tabel schedules punya kolom: staff_id, date, status (kode)
        $schedules = Schedule::where('staff_id', $staffId)
            ->whereBetween('schedule_date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
            ->get()
            ->keyBy(fn($item) => Carbon::parse($item->schedule_date)->format('Y-m-d'));

        // 4. Ambil DATA CUTI yang beririsan dengan minggu ini
        // Asumsi tabel leaves punya: staff_id, start_date, end_date, status
        $leaves = Leave::where('staff_id', $staffId)
            ->where('status', 'approved') // Hanya yang disetujui
            ->where(function ($query) use ($startOfWeek, $endOfWeek) {
                $query->whereBetween('start_date', [$startOfWeek, $endOfWeek])
                      ->orWhereBetween('end_date', [$startOfWeek, $endOfWeek])
                      ->orWhere(function ($q) use ($startOfWeek, $endOfWeek) {
                          $q->where('start_date', '<', $startOfWeek)
                            ->where('end_date', '>', $endOfWeek);
                      });
            })
            ->get();

        $days = [];

        // 5. Loop 7 Hari
        for ($i = 0; $i < 7; $i++) {
            $currentDate = $startOfWeek->copy()->addDays($i);
            $dateString = $currentDate->format('Y-m-d');
            
            $isToday = $currentDate->isSameDay($today);
            $isFuture = $currentDate->isAfter($today);

            // Ambil Data dari Collection di atas
            $presenceData = $presences->get($dateString);
            $scheduleData = $schedules->get($dateString);

            // Cek apakah tanggal ini masuk masa cuti
            $leaveData = $leaves->first(function ($leave) use ($currentDate) {
                return $currentDate->between(
                    Carbon::parse($leave->start_date), 
                    Carbon::parse($leave->end_date)
                );
            });

            // --- LOGIKA PENENTUAN STATUS ---
            
            // Default Values
            $status = 'unknown';
            $icon = 'heroicon-o-question-mark-circle';
            $color = 'text-gray-400';
            $label = '-';

            // 1. Prioritas Tertinggi: SUDAH HADIR
            if ($presenceData) {
                $status = 'present';
                $icon = 'heroicon-o-check-circle';
                $color = 'text-success-600 dark:text-success-400';
                $label = 'Hadir'; // Atau jam masuk: $presenceData->check_in
            } 
            // 2. Cek CUTI / IZIN
            elseif ($leaveData) {
                $status = 'leave';
                $icon = 'heroicon-o-clipboard-document-check'; // Icon kertas izin
                $color = 'text-info-500 dark:text-info-400';
                $label = 'Cuti / Izin'; // Atau $leaveData->type (Cuti Tahunan, Sakit, dll)
            }
            // 3. Cek JADWAL LIBUR (Kode 'L')
            elseif ($scheduleData && (
                $scheduleData->code === 'L' || 
                $scheduleData->is_holiday ||
                // Cek jika nama shift mengandung kata 'Libur' (case insensitive)
                ($scheduleData->shift && stripos($scheduleData->shift->name, 'Libur') !== false)
            )) { 
                $status = 'holiday';
                $icon = 'heroicon-o-face-smile'; // Icon Power sesuai request
                $color = 'text-info-500 dark:text-info-400';
                $label = 'Libur';
            }
            
            // 4. Hari Depan (Belum terjadi)
            elseif ($isFuture) {
                $status = 'future';
                $icon = 'heroicon-o-clock';
                $color = 'text-gray-300 dark:text-gray-600';
                $label = $scheduleData ? ($scheduleData->shift->name ?? 'Menunggu') : 'Menunggu';
            }
            // 5. Hari Ini Belum Presensi (Tapi Jadwal Masuk)
            elseif ($isToday) {
                $status = 'pending';
                $icon = 'heroicon-o-clock';
                $color = 'text-warning-500 dark:text-warning-400';
                $label = 'Belum Presensi';
            }
            // 6. Hari Lewat & Tanpa Keterangan (Alpha)
            else {
                // Pastikan dulu jadwalnya bukan Libur (jika scheduleData null, asumsi masuk atau libur? sesuaikan kebijakan)
                // Disini asumsinya jika tidak ada jadwal = dianggap masuk atau Alpha
                $status = 'absent';
                $icon = 'heroicon-o-x-circle';
                $color = 'text-danger-600 dark:text-danger-400';
                $label = 'Tidak Hadir';
            }

            $days[] = [
                'date_idx' => $currentDate->format('d'),
                'day_name' => $currentDate->translatedFormat('l'),
                'is_today' => $isToday,
                'status' => $status,
                'icon' => $icon,
                'color' => $color,
                'label' => $label,
            ];
        }

        return [
            'days' => $days,
        ];
    }
}
