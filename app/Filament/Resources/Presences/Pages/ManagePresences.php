<?php

namespace App\Filament\Resources\Presences\Pages;

use App\Filament\Resources\Presences\PresenceResource;
use App\Livewire\DeviceCaptureWidget;
use App\Models\Presence;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;
use Mpdf\Mpdf;

class ManagePresences extends ManageRecords
{
    protected static string $resource = PresenceResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            DeviceCaptureWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ip-status')
                ->label('IP Info')
                ->color('gray')
                ->icon('heroicon-o-signal')
                ->modalHeading('Status Jaringan')
                ->modalWidth('md')
                ->modalContent(view('filament.components.current-ip')),
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
            Action::make('check_in_gps')
                ->label('Check In dengan GPS')
                ->icon('heroicon-o-map-pin')
                ->color('info')
                ->visible(fn () => Presence::where('staff_id', Auth::user()->staff_id)->whereDate('presence_date', now()->toDateString())->count() === 0)
                ->modalHeading('Absensi via Koordinat Lokasi')
                ->modalWidth('2xl')
                ->modalSubmitAction(false)
                ->modalCancelAction(false)
                ->modalContent(fn () => view('filament.components.map-modal')),
            Action::make('check_out_gps')
                ->label('Check Out dengan GPS')
                ->icon('heroicon-o-map-pin')
                ->color('info')
                ->visible(fn () => Presence::where('staff_id', Auth::user()->staff_id)->whereDate('presence_date', now()->toDateString())->whereNull('check_out')->count() > 0)
                ->modalHeading('Absensi via Koordinat Lokasi')
                ->modalWidth('2xl')
                ->modalSubmitAction(false)
                ->modalCancelAction(false)
                ->modalContent(fn () => view('filament.components.map-modal')),
                
        ];
    }
}
