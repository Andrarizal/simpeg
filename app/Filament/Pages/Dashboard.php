<?php

namespace App\Filament\Pages;

use App\Livewire\DeviceCaptureWidget;
use App\Models\Presence;
use App\Models\Schedule;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class Dashboard extends BaseDashboard
{
    public function getTitle(): string
    {
        $user = Auth::user();
        return 'Selamat Datang, ' . $user->name;
    }
    
    public function getColumns(): int | array
    {
        return 2;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DeviceCaptureWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('check_in')
                ->label('Check In')
                ->icon('heroicon-o-finger-print')
                ->visible(fn () => Presence::where('staff_id', Auth::user()->staff_id)->whereDate('presence_date', now()->toDateString())->count() == 0)
                ->action(function () {
                    $device = session('device_info');
                    $today = now()->toDateString();

                    if (!$device) {
                        Notification::make()
                            ->title('Data perangkat belum terdeteksi!')
                            ->danger()
                            ->send();
                        return;
                    }

                    if (substr($device['ip'], 0, 6) !== setting('ip')) {
                        Notification::make()
                            ->title('Hubungkan dengan jaringan kantor!')
                            ->danger()
                            ->send();
                        return;
                    }

                    $sameDeviceToday = Presence::where('fingerprint', $device['device_id'])
                        ->whereDate('presence_date', $today)
                        ->exists();

                    if ($sameDeviceToday) {
                        Notification::make()
                            ->title('Perangkat telah digunakan untuk check-in hari ini!')
                            ->danger()
                            ->send();
                        return;
                    }

                    $data = [
                        'staff_id' => Auth::user()->staff_id,
                        'presence_date' => now()->toDateString(),
                        'check_in' => now()->toTimeString(),
                        'method' => 'network',
                        'ip' => $device['ip'],
                        'fingerprint' => $device['device_id'],
                    ];

                    Presence::create($data);

                    Notification::make()
                        ->title('Check-in berhasil!')
                        ->success()
                        ->send();
                }),
            Action::make('check_out')
                ->label('Check Out')
                ->icon('heroicon-o-finger-print')
                ->visible(fn () => Presence::where('staff_id', Auth::user()->staff_id)->whereDate('presence_date', now()->toDateString())->whereNull('check_out')->count() > 0)
                ->action(function () {
                    $today = now()->toDateString();
                    $presence = Presence::where('staff_id', Auth::user()->staff_id)->whereDate('presence_date', $today)->first();
                    $presence->check_out = now()->toTimeString();
                    $presence->save();

                    Notification::make()
                        ->title('Check-out berhasil!')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getSubheading(): string|Htmlable|null
    {
        $schedule = Schedule::where('staff_id', Auth::user()->staff_id)
                        ->whereDate('schedule_date', Carbon::now())
                        ->first();

        if (!$schedule) return null;

        $shift = $schedule->shift;

        $start = Carbon::parse($shift->start_time ?? '00:00:00')->format('H:i');
        $end   = Carbon::parse($shift->end_time ?? '00:00:00')->format('H:i');

        $shiftItem = "
            <div class='flex items-center gap-1 whitespace-nowrap bg-gray-100 dark:bg-white/5 px-2 py-1 rounded-md border border-gray-200 dark:border-white/10'>
                <span class='font-bold text-primary-600 dark:text-primary-400'>Jadwal Hari ini:</span>
                <span class='text-gray-700 dark:text-gray-300'>{$start}-{$end} ($shift->code)</span>
            </div>
        ";

        return new HtmlString("
            <div class='flex flex-wrap items-center gap-2 mt-2 text-xs'>
                <div class='flex items-center justify-center w-6 h-6 bg-gray-100 dark:bg-gray-800 rounded-full shrink-0'>
                    <svg class='w-4 h-4 text-gray-500' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor'>
                        <path fill-rule='evenodd' d='M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z' clip-rule='evenodd' />
                    </svg>
                </div>
                
                {$shiftItem}
            </div>
        ");
    }
}