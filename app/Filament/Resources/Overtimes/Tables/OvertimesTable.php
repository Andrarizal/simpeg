<?php

namespace App\Filament\Resources\Overtimes\Tables;

use App\Models\Overtime;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class OvertimesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $query = Overtime::query();

                $query->where('staff_id', Auth::user()->staff_id)
                    ->orderBy('overtime_date', 'DESC')
                    ->orderBy('start_time', 'DESC');

                return $query;
            })
            ->columns([
                TextColumn::make('overtime_date')->label('Tanggal'),
                TextColumn::make('command')->label('Perintah'),
                TextColumn::make('start_time')
                    ->label('Mulai'),
                TextColumn::make('end_time')
                    ->label('Selesai')
                    ->getStateUsing(function ($record) {
                        return $record->end_time ?: '---';
                    }),

                TextColumn::make('hours')
                    ->label('Total Jam')
                    ->state(function ($record) {
                        if (! $record || ! $record->end_time) {
                            return '---';
                        }

                        $total = $record->getTotalHours();
                        return $total ? "{$total} jam" : '-';
                    }),
                IconColumn::make('is_known')
                    ->label('Mengetahui Atasan')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => $record->is_known ?? 'null')
                    ->icon(fn ($state) => match ($state) {
                        2 => 'heroicon-o-check-circle',
                        1 => 'heroicon-o-check-circle',
                        0 => 'heroicon-o-x-circle',
                        'null' => 'heroicon-o-clock',
                    })
                    ->color(fn ($state) => match ($state) {
                        2 => 'info',
                        1 => 'success',
                        0 => 'danger',
                        'null' => 'gray',
                    })
                    ->tooltip(fn ($state) => match ($state) {
                        2 => 'Diketahui Koordinator',
                        1 => 'Diketahui Kepala Unit',
                        0 => 'Ditolak',
                        'null' => 'Belum direspon',
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
                        1 => 'Diverifikasi',
                        0 => 'Ditolak',
                        'null' => 'Belum direspon',
                    }),
            ])
            ->filters([
                SelectFilter::make('month_year')
                    ->label('Bulan')
                    ->options(
                        collect(range(0, 11))
                            ->mapWithKeys(fn($i) => [
                                now()->subMonths($i)->format('Y-m') =>
                                    now()->subMonths($i)->translatedFormat('F Y'),
                            ])
                    )
                    ->default(now()->format('Y-m'))
                    ->query(function (Builder $query, $data) {
                        $query->where('month_year', $data['value']);
                    })
                    ->indicateUsing(function ($data) {
                        return [
                            'Bulan: ' . Carbon::parse($data['value'])->translatedFormat('F Y'),
                        ];
                    })
                    ->selectablePlaceholder(false)
                    ->native(false),
            ])
            ->hiddenFilterIndicators()
            ->recordActions([
                Action::make('selesai')
                    ->label('Selesai')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn($record) => is_null($record->end_time))
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->end_time = now()->format('H:i');
                        $record->calculateTotalHours();
                        $record->save();

                        Notification::make()
                            ->title('Lembur diselesaikan')
                            ->success()
                            ->send();
                    }),
                    DeleteAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('overtime_date', 'desc');
    }
}
