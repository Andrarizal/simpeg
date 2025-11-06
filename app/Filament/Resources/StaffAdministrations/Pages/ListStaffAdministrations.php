<?php

namespace App\Filament\Resources\StaffAdministrations\Pages;

use App\Filament\Resources\StaffAdministrations\StaffAdministrationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStaffAdministrations extends ListRecords
{
    protected static string $resource = StaffAdministrationResource::class;
}
