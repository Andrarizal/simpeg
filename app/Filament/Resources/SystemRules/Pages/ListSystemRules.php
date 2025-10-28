<?php

namespace App\Filament\Resources\SystemRules\Pages;

use App\Filament\Resources\SystemRules\SystemRuleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSystemRules extends ListRecords
{
    protected static string $resource = SystemRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
