<?php

namespace App\Filament\Resources\StaffAdministrations\Pages;

use App\Filament\Resources\StaffAdministrations\StaffAdministrationResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditStaffAdministration extends EditRecord
{
    protected static string $resource = StaffAdministrationResource::class;
    protected static ?string $title = 'Perbarui Administrasi Pegawai';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
        ];
    }
}
