<?php

namespace App\Filament\Resources\OnCalls\Pages;

use App\Filament\Resources\OnCalls\OnCallResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageOnCalls extends ManageRecords
{
    protected static string $resource = OnCallResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Perintahkan On Call')
                ->visible(fn () => !OnCallResource::isSubordinate()),
        ];
    }
}
