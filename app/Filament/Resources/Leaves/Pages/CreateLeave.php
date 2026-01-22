<?php

namespace App\Filament\Resources\Leaves\Pages;

use App\Filament\Resources\Leaves\LeaveResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateLeave extends CreateRecord
{
    protected static string $resource = LeaveResource::class;

    protected ?string $heading = 'Ajukan Cuti/Izin';

    protected function getHeaderActions(): array
    {
        return [
            parent::getSubmitFormAction()
                ->label('Ajukan'),
            parent::getCancelFormAction()
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
