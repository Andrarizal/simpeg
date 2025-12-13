<?php

namespace App\Filament\Resources\Leaves\Tables;

use App\Filament\Resources\Leaves\LeaveResource;
use App\Models\Leave;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ReplacerTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $query = Leave::query();

                $query->where('replacement_id', Auth::user()->staff_id)
                    ->orderBy('start_date', 'DESC');
                return $query;
            })
            ->columns([
                TextColumn::make('type')
                    ->label('Jenis')
                    ->sortable(),
                TextColumn::make('staff.name')
                    ->label('Nama')
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label('Dari Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Sampai Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('remaining')
                    ->label('Sisa Cuti')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('replacement.name')
                    ->label('Nama Pengganti')
                    ->sortable(),
                IconColumn::make('is_replaced')
                    ->label('Bersedia')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => $record->is_replaced ?? 'null')
                    ->icon(fn ($state) => match ($state) {
                        1 => 'heroicon-o-check-circle',
                        0 => 'heroicon-o-x-circle',
                        'null' => 'heroicon-o-clock',
                    })
                    ->color(fn ($state) => match ($state) {
                        1 => 'success',
                        0 => 'danger',
                        'null' => 'gray',
                    })
                    ->tooltip(fn ($state) => match ($state) {
                        1 => 'Disetujui',
                        0 => 'Ditolak',
                        'null' => 'Belum direspon',
                    }),
                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(function ($state, $record) {
                        if ($state == 'Disetujui Kepala Seksi' && optional($record->staff->chair)->level == 3) {
                            return 'Diketahui Kepala Seksi';
                        }
                        return $state;
                    })
                    ->badge()
                    ->alignCenter()
                    ->color(function ($state, $record) {
                        $display = $state;
                        if ($state == 'Disetujui Kepala Seksi' && optional($record->staff->chair)->level == 3) {
                            $display = 'Diketahui Kepala Seksi';
                        }

                        if (str_contains($display, 'Disetujui')) {
                            return 'success';
                        } else if (str_contains($display, 'Diketahui')) {
                            return 'info';
                        } else if (str_contains($display, 'Menunggu')) {
                            return 'warning';
                        } else if (str_contains($display, 'Ditolak')) {
                            return 'danger';
                        } else {
                            return 'gray';
                        }
                    }),
                IconColumn::make('is_verified')
                    ->label('Verifikasi SDM')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => $record->is_verified ?? 'null')
                    ->icon(fn ($state) => match ($state) {
                        1 => 'heroicon-o-check-circle',
                        0 => 'heroicon-o-x-circle',
                        'null' => 'heroicon-o-clock',
                    })
                    ->color(fn ($state) => match ($state) {
                        1 => 'info',
                        0 => 'danger',
                        'null' => 'gray',
                    })
                    ->tooltip(fn ($state) => match ($state) {
                        1 => 'Disetujui',
                        0 => 'Ditolak',
                        'null' => 'Belum direspon',
                    }),
                TextColumn::make('approver.name')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('adverb')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Bersedia')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->is_replaced || $record->status == 'Ditolak' || $record->is_verified == 0 ? false : true)
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'is_replaced' => true,
                            'replacement_at' => Carbon::now()
                        ]);

                        Notification::make()
                            ->title($record->type . ' Anda bersedia digantikan')
                            ->body('Pengganti Anda telah menyatakan ketersediaannya pada ' . $record->type . ' Anda tanggal ' . Carbon::parse($record->start_date)->translatedFormat('d F Y'))
                            ->success()
                            ->actions([
                                Action::make('read')
                                    ->button()
                                    ->url(LeaveResource::getUrl('index'))
                                    ->markAsRead()
                            ])
                            ->sendToDatabase($record->staff->user);

                        Notification::make()
                            ->title('Ketersediaan berhasil ditambahkan')
                            ->success()
                            ->send();
                    }),
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                ]),
            ]);
    }
}
