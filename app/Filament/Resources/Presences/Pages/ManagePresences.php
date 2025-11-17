<?php

namespace App\Filament\Resources\Presences\Pages;

use App\Filament\Resources\Presences\PresenceResource;
use App\Livewire\DeviceCaptureWidget;
use App\Models\Presence;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
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
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('warning')
                ->action(function ($livewire) {
                    $month = $livewire->tableFilters['month_year']['value'] ?? now()->format('Y-m');

                    $data = Presence::query()
                        ->with(['staff.chair', 'staff.unit'])
                        ->where('staff_id', Auth::user()->staff_id)
                        ->whereMonth('presence_date', substr($month, 5, 2))
                        ->whereYear('presence_date', substr($month, 0, 4))
                        ->orderBy('presence_date')
                        ->get();

                    $html = view('exports.presences', compact('data', 'month'))->render();

                    $mpdf = new Mpdf([
                        'mode' => 'utf-8',
                        'format' => 'A4',
                        'margin_left'   => 25, // 2.5 cm
                        'margin_right'  => 20, // 2 cm
                        'margin_top'    => 25, // 2.5 cm
                        'margin_bottom' => 20, // 2 cm
                    ]);

                    $mpdf->WriteHTML($html);

                    $pdfData = $mpdf->Output('', 'S');

                    return response()->streamDownload(function () use ($pdfData) {
                        echo $pdfData;
                    }, "rekap-absen-$month.pdf");
                }),
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
