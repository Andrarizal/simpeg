<?php

namespace App\Filament\Resources\SystemRules\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SystemRulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')
                    ->searchable(),
                TextColumn::make('value')
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
