<?php

namespace App\Filament\Resources\StaffAdministrations\Pages;

use App\Filament\Resources\StaffAdministrations\StaffAdministrationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditStaffAdministration extends EditRecord
{
    protected static string $resource = StaffAdministrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
