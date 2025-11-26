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
                    ->label('Surat Izin Praktek')
                    ->formatStateUsing(fn ($state) => $state ? '📄 ' . basename($state) : '-')
                    ->suffixAction(
                        Action::make('show')
                            ->icon('heroicon-o-eye')
                            ->label('Lihat')
                            ->button()
                            ->modalWidth('5xl')
                            ->modalHeading(fn ($record) => 'Preview SIP - ' . $record->staff->name)
                            ->modalSubmitAction(false)
                            ->modalCancelAction(false)
                            ->modalContent(function ($record) {
                                return view('filament.components.preview-pdf-2', [
                                    'url' => route('preview.administration', [
                                        'model' => 'administration',
                                        'id' => $record->id,
                                        'field' => 'sip'
                                    ])
                                ]);
                            })
                            ->color('warning')
                            ->outlined()
                    )
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->placeholder('Belum Mengupload'),
                TextEntry::make('Surat Tanda Registrasi')
                    ->label('Surat Tanda Registrasi')
                    ->formatStateUsing(fn ($state) => $state ? '📄 ' . basename($state) : '-')
                    ->suffixAction(
                        Action::make('show')
                            ->icon('heroicon-o-eye')
                            ->label('Lihat')
                            ->button()
                            ->modalWidth('5xl')
                            ->modalHeading(fn ($record) => 'Preview STR - ' . $record->staff->name)
                            ->modalSubmitAction(false)
                            ->modalCancelAction(false)
                            ->modalContent(function ($record) {
                                return view('filament.components.preview-pdf-2', [
                                    'url' => route('preview.administration', [
                                        'model' => 'administration',
                                        'id' => $record->id,
                                        'field' => 'str'
                                    ])
                                ]);
                            })
                            ->color('warning')
                            ->outlined()
                    )
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->placeholder('Belum Mengupload'),
                TextEntry::make('mcu')
                    ->label('Medical Check Up')
                    ->formatStateUsing(fn ($state) => $state ? '📄 ' . basename($state) : '-')
                    ->suffixAction(
                        Action::make('show')
                            ->icon('heroicon-o-eye')
                            ->label('Lihat')
                            ->button()
                            ->modalWidth('5xl')
                            ->modalHeading(fn ($record) => 'Preview MCU - ' . $record->staff->name)
                            ->modalSubmitAction(false)
                            ->modalCancelAction(false)
                            ->modalContent(function ($record) {
                                return view('filament.components.preview-pdf-2', [
                                    'url' => route('preview.administration', [
                                        'model' => 'administration',
                                        'id' => $record->id,
                                        'field' => 'mcu'
                                    ])
                                ]);
                            })
                            ->color('warning')
                            ->outlined()
                    )
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->placeholder('Belum Mengupload'),
                TextEntry::make('spk')
                    ->label('Surat Penugasan Klinis')
                    ->formatStateUsing(fn ($state) => $state ? '📄 ' . basename($state) : '-')
                    ->suffixAction(
                        Action::make('show')
                            ->icon('heroicon-o-eye')
                            ->label('Lihat')
                            ->button()
                            ->modalWidth('5xl')
                            ->modalHeading(fn ($record) => 'Preview SPK - ' . $record->staff->name)
                            ->modalSubmitAction(false)
                            ->modalCancelAction(false)
                            ->modalContent(function ($record) {
                                return view('filament.components.preview-pdf-2', [
                                    'url' => route('preview.administration', [
                                        'model' => 'administration',
                                        'id' => $record->id,
                                        'field' => 'spk'
                                    ])
                                ]);
                            })
                            ->color('warning')
                            ->outlined()
                    )
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->placeholder('Belum Mengupload'),
                TextEntry::make('rkk')
                    ->label('Rencana Kewenangan Klinis')
                    ->formatStateUsing(fn ($state) => $state ? '📄 ' . basename($state) : '-')
                    ->suffixAction(
                        Action::make('show')
                            ->icon('heroicon-o-eye')
                            ->label('Lihat')
                            ->button()
                            ->modalWidth('5xl')
                            ->modalHeading(fn ($record) => 'Preview RKK - ' . $record->staff->name)
                            ->modalSubmitAction(false)
                            ->modalCancelAction(false)
                            ->modalContent(function ($record) {
                                return view('filament.components.preview-pdf-2', [
                                    'url' => route('preview.administration', [
                                        'model' => 'administration',
                                        'id' => $record->id,
                                        'field' => 'rkk'
                                    ])
                                ]);
                            })
                            ->color('warning')
                            ->outlined()
                    )
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->placeholder('Belum Mengupload'),
                TextEntry::make('utw')
                    ->label('Uraian Tugas dan Wewenang')
                    ->formatStateUsing(fn ($state) => $state ? '📄 ' . basename($state) : '-')
                    ->suffixAction(
                        Action::make('show')
                            ->icon('heroicon-o-eye')
                            ->label('Lihat')
                            ->button()
                            ->modalWidth('5xl')
                            ->modalHeading(fn ($record) => 'Preview UTW - ' . $record->staff->name)
                            ->modalSubmitAction(false)
                            ->modalCancelAction(false)
                            ->modalContent(function ($record) {
                                return view('filament.components.preview-pdf-2', [
                                    'url' => route('preview.administration', [
                                        'model' => 'administration',
                                        'id' => $record->id,
                                        'field' => 'utw'
                                    ])
                                ]);
                            })
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
