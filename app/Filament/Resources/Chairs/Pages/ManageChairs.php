<?php

namespace App\Filament\Resources\Chairs\Pages;

use App\Filament\Resources\Chairs\ChairResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageChairs extends ManageRecords
{
    protected static string $resource = ChairResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
