<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Alamat Email')
                    ->searchable(),
                TextColumn::make('role.name')
                    ->label('Role')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn ($record) => $record->role_id != 1),
                DeleteAction::make()
                    ->visible(fn ($record) => $record->role_id != 1),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    
                ]),
            ]);
    }
}
