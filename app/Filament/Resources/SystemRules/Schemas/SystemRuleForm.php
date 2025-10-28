<?php

namespace App\Filament\Resources\SystemRules\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SystemRuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('group')
                    ->required(),
                TextInput::make('key')
                    ->required(),
                Textarea::make('value')
                    ->columnSpanFull(),
                TextInput::make('type')
                    ->required()
                    ->default('string'),
                TextInput::make('description'),
            ]);
    }
}
