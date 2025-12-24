<?php

namespace App\Filament\Widgets;

use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Overtime;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;

class OvertimeTable extends TableWidget
{
    protected static ?int $sort = 9;

    public function table(Table $table): Table
    {
        return $table
            ->heading('3 Pengajuan Lembur Terbaru')
            ->query(function (): Builder {
                $query = Overtime::query();

                $query->where('staff_id', Auth::user()->staff_id)
                    ->orderBy('overtime_date', 'DESC')
                    ->limit(3);

                return $query;
            })
            ->columns([
                TextColumn::make('overtime_date')->label('Tanggal')->date(),
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
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->paginated(false)
            ->searchable(false);
    }

    protected int|string|array $columnSpan = 'full';
}
