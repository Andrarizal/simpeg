<?php

namespace App\Filament\Resources\Overtimes\Pages;

use App\Filament\Resources\Overtimes\OvertimeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOvertime extends CreateRecord
{
    protected static string $resource = OvertimeResource::class;

    public static ?string $title = 'Ajukan Lembur';
}
