<?php

namespace App\Filament\Resources\Overtimes\Tables;

use App\Filament\Resources\Overtimes\OvertimeResource;
use App\Models\Overtime;
use App\Models\Staff;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ApproveTable
{
    public static function configure(Table $table, ?Staff $staff): Table
    {
        return $table
            ->query(fn() => Overtime::query()->where('staff_id', $staff->id))
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
                Action::make('approve')
                    ->label('Ketahui')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(function ($record) {
                        if (Auth::user()->role_id === 1) return false;

                        if (Auth::user()->staff->chair->level === 4){
                            return $record->is_known > 0 ? false : true;
                        } else if (Auth::user()->staff->chair->level === 3){
                            return $record->is_known === 1 || (!$record->is_known && !$record->staff->unit->leader_id)  ? true : false;
                        }
                        return false;
                    })
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $user = Auth::user();
                        $user->staff_id = $user->staff_id ?? 1;

                        if ($user->staff->chair->level === 4){
                            $record->update([
                                'is_known' => 1,
                            ]);
                        } else {
                            $record->update([
                                'is_known' => 2,
                            ]);
                        }

                        $record->update([
                            'known_by' => $user->staff_id,
                            'known_at' => Carbon::now()
                        ]);

                        Notification::make()
                            ->title('Pengajuan Lembur Diketahui')
                            ->body('Lembur Anda untuk ' . Carbon::parse($record->overtime_date)->translatedFormat('d F Y') . ' telah diketahui oleh ' . $user->staff->chair->level === 4 ? 'Kepala Unit' : 'Koordinator')
                            ->success()
                            ->actions([
                                Action::make('read')
                                    ->button()
                                    ->url(OvertimeResource::getUrl('index'))
                                    ->markAsRead()
                            ])
                            ->sendToDatabase($record->staff->user);

                        Notification::make()
                            ->title('Lembur diketahui')
                            ->success()
                            ->send();
                    }),
                Action::make('verification')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check')
                    ->color('info')
                    ->visible(fn ($record) => ($record->is_verified || Auth::user()->staff->chair->level < 4 || Auth::user()->role_id > 1) ? false : true)
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $recipient = $record->staff->user;

                        $record->update([
                            'is_verified' => 1,
                            'verified_by' => Auth::user()->staff_id,
                            'verified_at' => Carbon::now()
                        ]);

                        Notification::make()
                            ->title('Pengajuan Lembur Diverifikasi')
                            ->body('Lembur Anda untuk ' . Carbon::parse($record->overtime_date)->translatedFormat('d F Y') . ' telah diverifikasi SDM')
                            ->success()
                            ->actions([
                                Action::make('read')
                                    ->button()
                                    ->url(OvertimeResource::getUrl('index'))
                                    ->markAsRead()
                            ])
                            ->sendToDatabase($recipient);

                        Notification::make()
                            ->title('Lembur diverifikasi')
                            ->success()
                            ->send();
                    }),
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('ketahui')
                    ->label('Ketahui')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn () => Auth::user()->staff->chair->level === 3 || (Auth::user()->staff->chair->level === 4 && Auth::user()->staff->unit->leader_id === Auth::user()->staff->chair_id))
                    ->disabled(fn (Collection $records) => !$records->doesntContain('is_known', 1) || !$records->doesntContain('is_known', 2))
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            $user = Auth::user();
                            $user->staff_id = $user->staff_id ?? 1;

                            if ($user->staff->chair->level === 4){
                                $record->update([
                                    'is_known' => 1,
                                ]);
                            } else {
                                $record->update([
                                    'is_known' => 2,
                                ]);
                            }
                        }

                        Notification::make()
                            ->title('Pengajuan Lembur Diketahui')
                            ->body('Lembur Anda untuk bulan ' . Carbon::parse($records[0]->overtime_date)->translatedFormat('F Y') . ' telah diketahui oleh ' . $user->staff->chair->level === 4 ? 'Kepala Unit' : 'Koordinator')
                            ->success()
                            ->actions([
                                Action::make('read')
                                    ->button()
                                    ->url(OvertimeResource::getUrl('index'))
                                    ->markAsRead()
                            ])
                            ->sendToDatabase($record->staff->user);

                        Notification::make()
                            ->title('Data lembur ditandai diketahui.')
                            ->success()
                            ->send();
                    }),

                    BulkAction::make('verifikasi')
                        ->label('Verifikasi ')
                        ->color('info')
                        ->requiresConfirmation()
                        ->visible(fn () => Auth::user()->role_id === 1 && Auth::user()->staff->chair->level === 4)
                        ->disabled(fn (Collection $records) => !$records->doesntContain('is_verified', 1))
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update([
                                    'is_verified' => 1,
                                ]);
                            }

                        Notification::make()
                            ->title('Pengajuan Lembur Diverifikasi')
                            ->body('Lembur Anda untuk bulan ' . Carbon::parse($records[0]->overtime_date)->translatedFormat('F Y') . ' telah diverifikasi SDM')
                            ->success()
                            ->actions([
                                Action::make('read')
                                    ->button()
                                    ->url(OvertimeResource::getUrl('index'))
                                    ->markAsRead()
                            ])
                            ->sendToDatabase($record->staff->user);

                            Notification::make()
                                ->title('Data lembur diverifikasi.')
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }
}
