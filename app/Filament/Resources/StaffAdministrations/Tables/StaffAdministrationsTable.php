<?php

namespace App\Filament\Resources\StaffAdministrations\Tables;

use App\Filament\Resources\StaffAdministrations\StaffAdministrationResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class StaffAdministrationsTable
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
                TextColumn::make('staff.name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('sip')
                    ->label('SIP')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => !empty($record?->sip))
                    ->icon(fn ($record) => $record?->sip ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn ($record) => $record?->sip ? 'success' : 'danger')
                    ->tooltip(fn ($record) => $record?->sip ? 'Sudah Upload' : 'Belum Upload'),
                IconColumn::make('str')
                    ->label('STR')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => !empty($record?->str))
                    ->icon(fn ($record) => $record?->str ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn ($record) => $record?->str ? 'success' : 'danger')
                    ->tooltip(fn ($record) => $record?->str ? 'Sudah Upload' : 'Belum Upload'),
                IconColumn::make('mcu')
                    ->label('MCU')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => !empty($record?->mcu))
                    ->icon(fn ($record) => $record?->mcu ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn ($record) => $record?->mcu ? 'success' : 'danger')
                    ->tooltip(fn ($record) => $record?->mcu ? 'Sudah Upload' : 'Belum Upload'),
                IconColumn::make('spk')
                    ->label('SPK')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => !empty($record?->spk))
                    ->icon(fn ($record) => $record?->spk ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn ($record) => $record?->spk ? 'success' : 'danger')
                    ->tooltip(fn ($record) => $record?->spk ? 'Sudah Upload' : 'Belum Upload'),
                IconColumn::make('rkk')
                    ->label('RKK')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => !empty($record?->rkk))
                    ->icon(fn ($record) => $record?->rkk ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn ($record) => $record?->rkk ? 'success' : 'danger')
                    ->tooltip(fn ($record) => $record?->rkk ? 'Sudah Upload' : 'Belum Upload'),
                IconColumn::make('utw')
                    ->label('UTW')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => !empty($record?->utw))
                    ->icon(fn ($record) => $record?->utw ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn ($record) => $record?->utw ? 'success' : 'danger')
                    ->tooltip(fn ($record) => $record?->utw ? 'Sudah Upload' : 'Belum Upload'),
                IconColumn::make('is_verified')
                    ->label('Verifikasi SDM')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => !empty($record?->is_verified))
                    ->icon(fn ($record) => match ($record?->is_verified) {
                        1 => 'heroicon-o-check-circle',
                        0 => 'heroicon-o-x-circle',
                        null => 'heroicon-o-clock'
                    })
                    ->color(fn ($record) => match ($record?->is_verified) {
                        1 => 'info',
                        0 => 'danger',
                        null => 'gray'
                    })
                    ->tooltip(fn ($record) => match ($record?->is_verified) {
                        1 => 'Disetujui',
                        0 => 'Ditolak',
                        null => 'Menunggu'
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('verified')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check')
                    ->color('info')
                    ->visible(function ($record) {
                        if (Auth::user()->role_id === 1) {
                            return $record->is_verified ? false : true;
                        }
                        return false;
                    })
                    // ->visible(true)
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'is_verified' => 1,
                        ]);

                        Notification::make()
                            ->title('Administrasi Diverifikasi')
                            ->body('Administrasi Anda telah diverifikasi SDM')
                            ->success()
                            ->actions([
                                Action::make('read')
                                    ->button()
                                    ->url(StaffAdministrationResource::getUrl('view', [$record->staff_id]))
                                    ->markAsRead()
                            ])
                            ->sendToDatabase($record->staff->user);

                        Notification::make()
                            ->title('Administrasi diverifikasi')
                            ->success()
                            ->send();
                    }),
                ViewAction::make(),
                EditAction::make()
                    ->label('Perbarui'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
