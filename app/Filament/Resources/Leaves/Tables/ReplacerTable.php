<?php

namespace App\Filament\Resources\Leaves\Tables;

use App\Models\Leave;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
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
                    ->alignCenter()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Menunggu' => 'warning',
                        'Diketahui Kepala Unit' => 'info',
                        'Diketahui Koordinator' => 'primary',
                        'Disetujui Kepala Seksi' => 'success',
                        'Disetujui Direktur' => 'success',
                        'Ditolak' => 'danger',
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
                        1 => 'success',
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
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Bersedia')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->is_replaced ? false : true)
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'is_replaced' => true,
                        ]);
                    }),
                ViewAction::make(),
                DeleteAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
