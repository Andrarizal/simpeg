<?php

namespace App\Filament\Resources\StaffAdministrations\Tables;

use App\Filament\Resources\StaffAdministrations\StaffAdministrationResource;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
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
                TextColumn::make('staff.name')
                    ->label('Nama Lengkap')
                    ->searchable(),
                TextColumn::make('staff.group')
                    ->label('Tenaga')
                    ->formatStateUsing(fn ($record) => $record?->staff->group_id != 1 && $record?->staff->group_id < 9 ? 'Nakes' : 'Non-nakes')
                    ->searchable(),
                IconColumn::make('sip')
                    ->label('SIP')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => !empty($record?->sip))
                    ->icon(fn ($record) => match(true) {
                        (int) $record->staff->group_id >= 9 || $record->staff->group_id == 1 => null,
                        empty($record->sip) => 'heroicon-o-clock',
                        !empty($record->sip_expiry) && Carbon::parse($record->sip_expiry)->isPast() => 'heroicon-o-x-circle',
                        !empty($record->sip_expiry) && Carbon::parse($record->sip_expiry)->lte(now()->addMonths(6)) => 'heroicon-o-exclamation-triangle',
                        default => 'heroicon-o-check-circle',
                    })
                    ->color(fn ($record) => match(true) {
                        (int) $record->staff->group_id >= 9 || $record->staff->group_id == 1 => null,
                        empty($record->sip) => 'gray',
                        !empty($record->sip_expiry) && Carbon::parse($record->sip_expiry)->isPast() => 'danger',
                        !empty($record->sip_expiry) && Carbon::parse($record->sip_expiry)->lte(now()->addMonths(6)) => 'warning',
                        default => 'success',
                    })
                    ->tooltip(fn ($record) => match(true) {
                        (int) $record->staff->group_id >= 9 || $record->staff->group_id == 1 => 'Tidak Wajib (Non-Nakes)',
                        empty($record->sip) => 'Belum diupload',
                        !empty($record->sip_expiry) && Carbon::parse($record->sip_expiry)->isPast() => 'Sudah Kadaluarsa: ' . Carbon::parse($record->sip_expiry)->format('d M Y'),
                        !empty($record->sip_expiry) && Carbon::parse($record->sip_expiry)->lte(now()->addMonths(6)) => 'Segera Habis: ' . Carbon::parse($record->sip_expiry)->format('d M Y'),
                        default => 'Aktif sampai: ' . Carbon::parse($record->sip_expiry)->format('d M Y'),
                    }),
                IconColumn::make('str')
                    ->label('STR')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => !empty($record?->str))
                    ->icon(fn ($record) => match(true) {
                        (int) $record->staff->group_id >= 9 || $record->staff->group_id == 1 => null,
                        empty($record->str) => 'heroicon-o-clock',
                        !empty($record->str_expiry) && Carbon::parse($record->str_expiry)->isPast() => 'heroicon-o-x-circle',
                        !empty($record->str_expiry) && Carbon::parse($record->str_expiry)->lte(now()->addMonths(6)) => 'heroicon-o-exclamation-triangle',
                        default => 'heroicon-o-check-circle',
                    })
                    ->color(fn ($record) => match(true) {
                        (int) $record->staff->group_id >= 9 || $record->staff->group_id == 1 => null,
                        empty($record->str) => 'gray',
                        !empty($record->str_expiry) && Carbon::parse($record->str_expiry)->isPast() => 'danger',
                        !empty($record->str_expiry) && Carbon::parse($record->str_expiry)->lte(now()->addMonths(6)) => 'warning',
                        default => 'success',
                    })
                    ->tooltip(fn ($record) => match(true) {
                        (int) $record->staff->group_id >= 9 || $record->staff->group_id == 1 => 'Tidak Wajib (Non-Nakes)',
                        empty($record->str) => 'Belum diupload',
                        !empty($record->str_expiry) && Carbon::parse($record->str_expiry)->isPast() => 'Sudah Kadaluarsa: ' . Carbon::parse($record->str_expiry)->format('d M Y'),
                        !empty($record->str_expiry) && Carbon::parse($record->str_expiry)->lte(now()->addMonths(6)) => 'Segera Habis: ' . Carbon::parse($record->str_expiry)->format('d M Y'),
                        default => 'Aktif sampai: ' . Carbon::parse($record->str_expiry)->format('d M Y'),
                    }),
                IconColumn::make('spk')
                    ->label('SPK')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => !empty($record?->spk))
                    ->icon(fn ($record) => match(true) {
                        (int) $record->staff->group_id >= 9 || $record->staff->group_id == 1 => null,
                        empty($record->spk) => 'heroicon-o-clock',
                        !empty($record->spk_expiry) && Carbon::parse($record->spk_expiry)->isPast() => 'heroicon-o-x-circle',
                        !empty($record->spk_expiry) && Carbon::parse($record->spk_expiry)->lte(now()->addMonths(6)) => 'heroicon-o-exclamation-triangle',
                        default => 'heroicon-o-check-circle',
                    })
                    ->color(fn ($record) => match(true) {
                        (int) $record->staff->group_id >= 9 || $record->staff->group_id == 1 => null,
                        empty($record->spk) => 'gray',
                        !empty($record->spk_expiry) && Carbon::parse($record->spk_expiry)->isPast() => 'danger',
                        !empty($record->spk_expiry) && Carbon::parse($record->spk_expiry)->lte(now()->addMonths(6)) => 'warning',
                        default => 'success',
                    })
                    ->tooltip(fn ($record) => match(true) {
                        (int) $record->staff->group_id >= 9 || $record->staff->group_id == 1 => 'Tidak Wajib (Non-Nakes)',
                        empty($record->spk) => 'Belum diupload',
                        !empty($record->spk_expiry) && Carbon::parse($record->spk_expiry)->isPast() => 'Sudah Kadaluarsa: ' . Carbon::parse($record->spk_expiry)->format('d M Y'),
                        !empty($record->spk_expiry) && Carbon::parse($record->spk_expiry)->lte(now()->addMonths(6)) => 'Segera Habis: ' . Carbon::parse($record->spk_expiry)->format('d M Y'),
                        default => 'Aktif sampai: ' . Carbon::parse($record->spk_expiry)->format('d M Y'),
                    }),
                IconColumn::make('rkk')
                    ->label('RKK')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => !empty($record?->rkk))
                    ->icon(fn ($record) => match(true) {
                        (int) $record->staff->group_id >= 9 || $record->staff->group_id == 1 => null,
                        empty($record->rkk) => 'heroicon-o-clock',
                        !empty($record->rkk_expiry) && Carbon::parse($record->rkk_expiry)->isPast() => 'heroicon-o-x-circle',
                        !empty($record->rkk_expiry) && Carbon::parse($record->rkk_expiry)->lte(now()->addMonths(6)) => 'heroicon-o-exclamation-triangle',
                        default => 'heroicon-o-check-circle',
                    })
                    ->color(fn ($record) => match(true) {
                        (int) $record->staff->group_id >= 9 || $record->staff->group_id == 1 => null,
                        empty($record->rkk) => 'gray',
                        !empty($record->rkk_expiry) && Carbon::parse($record->rkk_expiry)->isPast() => 'danger',
                        !empty($record->rkk_expiry) && Carbon::parse($record->rkk_expiry)->lte(now()->addMonths(6)) => 'warning',
                        default => 'success',
                    })
                    ->tooltip(fn ($record) => match(true) {
                        (int) $record->staff->group_id >= 9 || $record->staff->group_id == 1 => 'Tidak Wajib (Non-Nakes)',
                        empty($record->rkk) => 'Belum diupload',
                        !empty($record->rkk_expiry) && Carbon::parse($record->rkk_expiry)->isPast() => 'Sudah Kadaluarsa: ' . Carbon::parse($record->rkk_expiry)->format('d M Y'),
                        !empty($record->rkk_expiry) && Carbon::parse($record->rkk_expiry)->lte(now()->addMonths(6)) => 'Segera Habis: ' . Carbon::parse($record->rkk_expiry)->format('d M Y'),
                        default => 'Aktif sampai: ' . Carbon::parse($record->rkk_expiry)->format('d M Y'),
                    }),
                IconColumn::make('mcu')
                    ->label('MCU')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => !empty($record?->mcu))
                    ->icon(fn ($record) => match(true) {
                        empty($record->mcu) => 'heroicon-o-clock',
                        !empty($record->mcu_expiry) && Carbon::parse($record->mcu_expiry)->isPast() => 'heroicon-o-x-circle',
                        !empty($record->mcu_expiry) && Carbon::parse($record->mcu_expiry)->lte(now()->addMonths(6)) => 'heroicon-o-exclamation-triangle',
                        default => 'heroicon-o-check-circle',
                    })
                    ->color(fn ($record) => match(true) {
                        empty($record->mcu) => 'gray',
                        !empty($record->mcu_expiry) && Carbon::parse($record->mcu_expiry)->isPast() => 'danger',
                        !empty($record->mcu_expiry) && Carbon::parse($record->mcu_expiry)->lte(now()->addMonths(6)) => 'warning',
                        default => 'success',
                    })
                    ->tooltip(fn ($record) => match(true) {
                        empty($record->mcu) => 'Belum diupload',
                        !empty($record->mcu_expiry) && Carbon::parse($record->mcu_expiry)->isPast() => 'Sudah Kadaluarsa: ' . Carbon::parse($record->mcu_expiry)->format('d M Y'),
                        !empty($record->mcu_expiry) && Carbon::parse($record->mcu_expiry)->lte(now()->addMonths(6)) => 'Segera Habis: ' . Carbon::parse($record->mcu_expiry)->format('d M Y'),
                        default => 'Aktif sampai: ' . Carbon::parse($record->mcu_expiry)->format('d M Y'),
                    }),
                IconColumn::make('utw')
                    ->label('UTW')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => !empty($record?->utw))
                    ->icon(fn ($record) => match(true) {
                        empty($record->utw) => 'heroicon-o-clock',
                        !empty($record->utw_expiry) && Carbon::parse($record->utw_expiry)->isPast() => 'heroicon-o-x-circle',
                        !empty($record->utw_expiry) && Carbon::parse($record->utw_expiry)->lte(now()->addMonths(6)) => 'heroicon-o-exclamation-triangle',
                        default => 'heroicon-o-check-circle',
                    })
                    ->color(fn ($record) => match(true) {
                        empty($record->utw) => 'gray',
                        !empty($record->utw_expiry) && Carbon::parse($record->utw_expiry)->isPast() => 'danger',
                        !empty($record->utw_expiry) && Carbon::parse($record->utw_expiry)->lte(now()->addMonths(6)) => 'warning',
                        default => 'success',
                    })
                    ->tooltip(fn ($record) => match(true) {
                        empty($record->utw) => 'Belum diupload',
                        !empty($record->utw_expiry) && Carbon::parse($record->utw_expiry)->isPast() => 'Sudah Kadaluarsa: ' . Carbon::parse($record->utw_expiry)->format('d M Y'),
                        !empty($record->utw_expiry) && Carbon::parse($record->utw_expiry)->lte(now()->addMonths(6)) => 'Segera Habis: ' . Carbon::parse($record->utw_expiry)->format('d M Y'),
                        default => 'Aktif sampai: ' . Carbon::parse($record->utw_expiry)->format('d M Y'),
                    }),
                IconColumn::make('is_verified')
                    ->label('Verifikasi')
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
            ->recordActions([
                Action::make('verified')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check')
                    ->color('info')
                    ->visible(function ($record) {
                        if (Auth::user()->role_id == 1) {
                            return $record->is_verified ? false : true;
                        }
                        return false;
                    })
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
                                    ->label('Lihat')
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
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(function ($record) {
                        if (Auth::user()->role_id == 1) {
                            return $record->is_verified ? false : true;
                        }
                        return false;
                    })
                    ->schema([
                        Textarea::make('note')
                            ->label('Alasan')
                            ->required()
                            ->rows(3),
                    ])
                    ->requiresConfirmation()
                    ->action(function ($data, $record) {
                        $record->update([
                            'is_verified' => 0,
                            'note' => $data['note']
                        ]);

                        Notification::make()
                            ->title('Administrasi Ditolak')
                            ->body('Alasan SDM: ' . $data['note'])
                            ->danger()
                            ->actions([
                                Action::make('read')
                                    ->label('Lihat')
                                    ->button()
                                    ->url(StaffAdministrationResource::getUrl('view', [$record->staff_id]))
                                    ->markAsRead()
                            ])
                            ->sendToDatabase($record->staff->user);

                        Notification::make()
                            ->title('Administrasi Ditolak')
                            ->success()
                            ->send();
                    }),
                ViewAction::make(),
                EditAction::make()
                    ->label('Perbarui'),
            ])
            ->checkIfRecordIsSelectableUsing(
                fn ($record) => $record->is_verified != 1
            )
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('verified')
                        ->label('Verifikasi yang dipilih')
                        ->icon('heroicon-o-check')
                        ->color('info')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update([
                                    'is_verified' => 1,
                                ]);

                                Notification::make()
                                    ->title('Administrasi Diverifikasi')
                                    ->body('Administrasi Anda telah diverifikasi SDM')
                                    ->success()
                                    ->actions([
                                        Action::make('read')
                                            ->label('Lihat')
                                            ->button()
                                            ->url(StaffAdministrationResource::getUrl('view', [$record->staff_id]))
                                            ->markAsRead()
                                    ])
                                    ->sendToDatabase($record->staff->user);
                            }

                            Notification::make()
                                ->title(count($records) . ' Administrasi diverifikasi')
                                ->success()
                                ->send();
                        }),
                    BulkAction::make('reject')
                        ->label('Tolak')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update([
                                    'is_verified' => 0,
                                ]);

                                Notification::make()
                                    ->title('Administrasi Ditolak')
                                    ->body('Administrasi Anda telah ditolak SDM')
                                    ->danger()
                                    ->actions([
                                        Action::make('read')
                                            ->label('Lihat')
                                            ->button()
                                            ->url(StaffAdministrationResource::getUrl('view', [$record->staff_id]))
                                            ->markAsRead()
                                    ])
                                    ->sendToDatabase($record->staff->user);
                            }

                            Notification::make()
                                ->title(count($records) . ' Administrasi ditolak')
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }
}
