<?php

namespace App\Filament\Resources\Presences\Pages;

use App\Filament\Resources\Presences\PresenceResource;
use App\Models\Presence;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePresences extends ManageRecords
{
    protected static string $resource = PresenceResource::class;

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
                ->color('success')
                ->icon('heroicon-o-finger-print')
                ->requiresConfirmation()
                ->action(function () {
                    $ssid = request()->input('ip');
                    if ($ssid !== 'NamaWifiKantor') {
                        dd(request()->ip());
                        return;
                    }

                    // Presence::create([
                    //     'staff_id' => Auth::user()->staff_id,
                    //     'date' => now()->toDateString(),
                    //     'clock_in' => now(),
                    //     'wifi_ssid' => $ssid,
                    // ]);
                }),
        ];
    }
}
