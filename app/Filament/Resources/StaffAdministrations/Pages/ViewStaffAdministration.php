<?php

namespace App\Filament\Resources\StaffAdministrations\Pages;

use App\Filament\Resources\StaffAdministrations\StaffAdministrationResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewStaffAdministration extends ViewRecord
{
    protected static string $resource = StaffAdministrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('verified')
                ->label('Verifikasi')
                ->color('info')
                ->visible(function ($record) {
                    if (Auth::user()->role_id == 1) {
                        return $record->is_verified ? false : true;
                    }
                    return false;
                })
                // ->visible(true)
                ->requiresConfirmation()
                ->action(function ($record) {
                    $record->update([
                        'is_verified' => 1,
                    ]);
                }),
            EditAction::make()
                ->label('Perbarui'),
        ];
    }
}
