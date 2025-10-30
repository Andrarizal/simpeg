<?php

namespace App\Filament\Resources\Staff\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StaffTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')
                    ->label('#')
                    ->rowIndex()
                    ->sortable(false)
                    ->toggleable(false)
                    ->width('80px'),
                TextColumn::make('nik')
                    ->label('NIK')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('nip')
                    ->label('NIP')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('birth_place')
                    ->label('Tempat Lahir')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sex')
                    ->label('Jenis Kelamin')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('marital')
                    ->label('Status Perkawinan')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->label('No. Telepon')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('other_phone')
                    ->label('No. Telepon Kerabat')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('other_phone_adverb')
                    ->label('Hubungan dengan Kerabat')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')
                    ->label('Email')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('address')
                    ->label('Alamat')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('entry_date')
                        ->label('Tanggal Masuk Kerja')
                        ->date()
                        ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('retirement_date')
                        ->label('Tanggal Pensiun')
                        ->date()
                        ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('chair.name')
                    ->label('Jabatan'),
                TextColumn::make('unit.name')
                    ->label('Unit Kerja')
                    ->searchable(),
                TextColumn::make('group.name')
                    ->label('Kelompok Tenaga Kerja')
                    ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('staffStatus.name')
                    ->label('Status')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
