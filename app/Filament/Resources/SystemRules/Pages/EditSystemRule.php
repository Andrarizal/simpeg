<?php

namespace App\Filament\Resources\SystemRules\Pages;

use App\Filament\Resources\SystemRules\SystemRuleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSystemRule extends EditRecord
{
    protected static string $resource = SystemRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
