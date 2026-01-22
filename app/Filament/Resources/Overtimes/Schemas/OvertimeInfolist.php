<?php

namespace App\Filament\Resources\Overtimes\Schemas;

use Carbon\Carbon;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class OvertimeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])
                    ->schema([
                        Group::make([
                            Section::make('Detail Pelaksanaan')
                                ->extraAttributes([
                                    'class' => implode(' ', [
                                        '[&_.fi-section-header]:bg-gradient-to-br',
                                        '[&_.fi-section-header]:from-emerald-500',
                                        '[&_.fi-section-header]:to-teal-600',
                                        '[&_.fi-section-header]:dark:from-emerald-900',
                                        '[&_.fi-section-header]:dark:to-teal-950',
                                        '[&_.fi-section-header]:rounded-t-2xl',
                                        '[&_.fi-section-header-heading]:!text-white',
                                        '[&_.fi-section-header-description]:!text-white/80',
                                        '[&_.fi-section-header_.fi-icon-btn]:!text-white',
                                    ])
                                ])
                                ->icon('heroicon-m-clipboard-document-list')
                                ->schema([
                                    TextEntry::make('staff.name')
                                        ->label('Nama Pegawai')
                                        ->icon('heroicon-m-user-circle')
                                        ->hiddenLabel()
                                        ->weight(FontWeight::SemiBold)
                                        ->size(TextSize::Small),

                                    TextEntry::make('command')
                                        ->label('Perintah / Uraian Tugas')
                                        ->markdown()
                                        ->prose()
                                        ->columnSpanFull()
                                        ->extraAttributes(['class' => 'bg-gray-50 dark:bg-gray-900 p-2 px-4 rounded-2xl border border-gray-200 dark:border-gray-800']),

                                    Group::make()
                                        ->extraAttributes([
                                            'class' => '[&_.fi-grid]:gap-2 [&_.fi-grid]:flex [&_.fi-grid]:flex-wrap -mt-4',
                                        ])
                                        ->schema([
                                            TextEntry::make('is_known')
                                                ->badge()
                                                ->hiddenLabel()
                                                ->default('null')
                                                ->formatStateUsing(fn ($state) => match ($state) {
                                                    2 => 'Diketahui Koordinator',
                                                    1 => 'Diketahui Kepala Unit',
                                                    0 => 'Ditolak Atasan',
                                                    'null' => 'Menunggu Diketahui Atasan',
                                                })
                                                ->color(fn ($state) => match ($state) {
                                                    1, 2 => 'success',
                                                    0 => 'danger',
                                                    'null' => 'warning',
                                                })
                                                ->icon(fn ($state) => match ($state) {
                                                    1, 2 => 'heroicon-m-check-circle',
                                                    0 => 'heroicon-m-x-circle',
                                                    'null' => 'heroicon-m-clock',
                                                }),

                                            TextEntry::make('is_verified')
                                                ->hiddenLabel()
                                                ->badge()
                                                ->default('null')
                                                ->formatStateUsing(fn ($state) => match ($state) {
                                                    1 => 'Terverifikasi SDM',
                                                    0 => 'Tidak Terverifikasi SDM',
                                                    'null' => 'Menunggu Verifikasi SDM',
                                                })
                                                ->color(fn ($state) => match ($state) {
                                                    1 => 'info',
                                                    0 => 'danger',
                                                    'null' => 'warning',
                                                })
                                                ->icon(fn ($state) => match ($state) {
                                                    1 => 'heroicon-m-shield-check',
                                                    0 => 'heroicon-m-exclamation-circle',
                                                    'null' => 'heroicon-m-clock',
                                                }),
                                        ]),
                                    TextEntry::make('note')
                                        ->label('Catatan Tambahan')
                                        ->placeholder('Tidak ada catatan')
                                        ->markdown()
                                        ->columnSpanFull(),
                                ]),
                        ])->columnSpan(['lg' => 2]),
                        Group::make([
                            Section::make('Waktu Lembur')
                                ->extraAttributes([
                                    'class' => implode(' ', [
                                        '[&_.fi-section-header]:bg-gradient-to-br',
                                        '[&_.fi-section-header]:from-emerald-500',
                                        '[&_.fi-section-header]:to-teal-600',
                                        '[&_.fi-section-header]:dark:from-emerald-900',
                                        '[&_.fi-section-header]:dark:to-teal-950',
                                        '[&_.fi-section-header]:rounded-t-2xl',
                                        '[&_.fi-section-header-heading]:!text-white',
                                        '[&_.fi-section-header-description]:!text-white/80',
                                        '[&_.fi-section-header_.fi-icon-btn]:!text-white',
                                    ])
                                ])
                                ->icon('heroicon-m-clock')
                                ->schema([
                                    TextEntry::make('overtime_date')
                                        ->label('Tanggal')
                                        ->date('d F Y')
                                        ->icon('heroicon-m-calendar'),
                                    TextEntry::make('time_range')
                                        ->label('Jam Pelaksanaan')
                                        ->icon('heroicon-m-clock')
                                        ->state(function ($record) {
                                            $start = Carbon::parse($record->start_time)->format('H:i');
                                            $end = $record->end_time ? Carbon::parse($record->end_time)->format('H:i') : '...';
                                            return "{$start} - {$end} WIB";
                                        }),
                                    TextEntry::make('hours')
                                        ->label('Durasi Total')
                                        ->color('primary')
                                        ->weight(FontWeight::Bold)
                                        ->size(TextSize::Large)
                                        ->state(function ($record) {
                                            if (! $record || ! $record->end_time) {
                                                return 'Sedang Berjalan';
                                            }
                                            $total = $record->getTotalHours();
                                            return $total ? "{$total} Jam" : '-';
                                        })
                                        ->badge(fn ($record) => $record->end_time ? false : true)
                                        ->color(fn ($record) => $record->end_time ? 'primary' : 'warning'),
                                ]),
                        ])->columnSpan(['lg' => 1]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
