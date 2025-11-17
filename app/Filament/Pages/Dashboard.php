<?php

namespace App\Filament\Pages;

use App\Models\Presence;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

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

    protected function getHeaderActions(): array
    {
        return [
            Action::make('check_in')
                ->label('Check In')
                ->icon('heroicon-o-finger-print')
                ->visible(fn () => Presence::where('staff_id', Auth::user()->staff_id)->whereDate('presence_date', now()->toDateString())->count() === 0)
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
}