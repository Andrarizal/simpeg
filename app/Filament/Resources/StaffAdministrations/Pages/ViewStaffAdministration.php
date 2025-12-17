<?php

namespace App\Filament\Resources\StaffAdministrations\Pages;

use App\Filament\Resources\StaffAdministrations\StaffAdministrationResource;
use App\Models\StaffAdministration;
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

    public function mount($record): void
    {
        if (Auth::user()->role_id == 2) {
            $recordModel = StaffAdministration::findOrFail($record);
            
            if ($recordModel->staff_id != Auth::user()->staff_id) {
                abort(403, 'Anda tidak memiliki akses ke data ini.');
            }
        }

        parent::mount($record);
    }
}
