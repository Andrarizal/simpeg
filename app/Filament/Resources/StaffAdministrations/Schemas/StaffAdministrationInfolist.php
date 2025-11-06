<?php

namespace App\Filament\Resources\StaffAdministrations\Schemas;

use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StaffAdministrationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('staff.name')
                    ->label('Nama Pegawai')
                    ->inlineLabel()
                    ->columnSpanFull(),
                TextEntry::make('sip')
                    ->label('SIP')
                    ->formatStateUsing(fn ($state) => $state ? '📄 ' . basename($state) : '-')
                    ->suffixAction(
                        Action::make('show')
                            ->icon('heroicon-o-eye')
                            ->label('Lihat')
                            ->button()
                            ->url(fn ($record) => asset('storage/' . $record->sip))
                            ->openUrlInNewTab()
                            ->color('warning')
                            ->outlined()
                    )
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->placeholder('Belum Mengupload'),
                TextEntry::make('str')
                    ->label('STR')
                    ->formatStateUsing(fn ($state) => $state ? '📄 ' . basename($state) : '-')
                    ->suffixAction(
                        Action::make('show')
                            ->icon('heroicon-o-eye')
                            ->label('Lihat')
                            ->button()
                            ->url(fn ($record) => asset('storage/' . $record->str))
                            ->openUrlInNewTab()
                            ->color('warning')
                            ->outlined()
                    )
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->placeholder('Belum Mengupload'),
                TextEntry::make('mcu')
                    ->label('MCU')
                    ->formatStateUsing(fn ($state) => $state ? '📄 ' . basename($state) : '-')
                    ->suffixAction(
                        Action::make('show')
                            ->icon('heroicon-o-eye')
                            ->label('Lihat')
                            ->button()
                            ->url(fn ($record) => asset('storage/' . $record->mcu))
                            ->openUrlInNewTab()
                            ->color('warning')
                            ->outlined()
                    )
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->placeholder('Belum Mengupload'),
                TextEntry::make('spk')
                    ->label('SPK')
                    ->formatStateUsing(fn ($state) => $state ? '📄 ' . basename($state) : '-')
                    ->suffixAction(
                        Action::make('show')
                            ->icon('heroicon-o-eye')
                            ->label('Lihat')
                            ->button()
                            ->url(fn ($record) => asset('storage/' . $record->spk))
                            ->openUrlInNewTab()
                            ->color('warning')
                            ->outlined()
                    )
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->placeholder('Belum Mengupload'),
                TextEntry::make('rkk')
                    ->label('RKK')
                    ->formatStateUsing(fn ($state) => $state ? '📄 ' . basename($state) : '-')
                    ->suffixAction(
                        Action::make('show')
                            ->icon('heroicon-o-eye')
                            ->label('Lihat')
                            ->button()
                            ->url(fn ($record) => asset('storage/' . $record->rkk))
                            ->openUrlInNewTab()
                            ->color('warning')
                            ->outlined()
                    )
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->placeholder('Belum Mengupload'),
                TextEntry::make('utw')
                    ->label('UTW')
                    ->formatStateUsing(fn ($state) => $state ? '📄 ' . basename($state) : '-')
                    ->suffixAction(
                        Action::make('show')
                            ->icon('heroicon-o-eye')
                            ->label('Lihat')
                            ->button()
                            ->url(fn ($record) => asset('storage/' . $record->utw))
                            ->openUrlInNewTab()
                            ->color('warning')
                            ->outlined()
                    )
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->placeholder('Belum Mengupload'),
                TextEntry::make('is_verified')
                    ->label('Verifikasi SDM')
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->formatStateUsing(fn ($state, $record) =>
                        ($record->is_verified === 1
                            ? '(Terverifikasi)'
                            : '(Belum Terverifikasi)')
                    ),
                TextEntry::make('adverb')
                    ->label('Catatan')
                    ->visible(fn ($state) => $state ? true : false),
            ]);
    }
}
