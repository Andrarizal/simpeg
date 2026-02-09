<?php

namespace App\Filament\Resources\ShiftExchanges\Pages;

use App\Filament\Resources\ShiftExchanges\ShiftExchangeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageShiftExchanges extends ManageRecords
{
    protected static string $resource = ShiftExchangeResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
