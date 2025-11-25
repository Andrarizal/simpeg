<?php

namespace App\Filament\Resources\PreStaff\Pages;

use App\Filament\Resources\PreStaff\PreStaffResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePreStaff extends ManageRecords
{
    protected static string $resource = PreStaffResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
