<?php

namespace App\Filament\Resources\Overtimes\Tables;

use App\Filament\Resources\Overtimes\OvertimeResource;
use App\Filament\Resources\Overtimes\Schemas\OvertimeInfolist;
use App\Models\Overtime;
use App\Models\Staff;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ApproveTable
{
    public static function configure(Table $table, ?Staff $staff): Table
    {
        return $table
            ->query(function () use ($staff) {
                return Overtime::query()->where('staff_id', $staff->id)->latest();
            })
            ->columns([
                TextColumn::make('overtime_date')
                    ->label('Tanggal')
                    ->date('d F Y')
                    ->alignCenter(),
                TextColumn::make('command')
                    ->label('Perintah')
                    ->wrap()
                    ->extraAttributes(['class' => 'min-w-xs']),
                TextColumn::make('start_time')
                    ->label('Mulai')
                    ->time('H:i')
                    ->alignCenter(),
                TextColumn::make('end_time')
                    ->label('Selesai')
                    ->placeholder('---')
                    ->alignCenter()
                    ->time(fn ($record) => $record->end_time ? 'H:i' : null),
                TextColumn::make('hours')
                    ->label('Total Jam')
                    ->alignCenter()
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
                            Indicator::make('Bulan: ' . Carbon::parse($data['value'])->translatedFormat('F Y'))
                                ->removable(false),
                        ];
                    })
                    ->selectablePlaceholder(false)
                    ->native(false),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Ketahui')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(function ($record) {
                        $user = Auth::user();
                        if ($user->role_id == 1) {
                            return false;
                        }
                        if (!$user->staff || !$user->staff->chair) {
                            return false;
                        }
                        $userLevel = $user->staff->chair->level;
                        switch ($userLevel) {
                            case 4:
                                return is_null($record->is_known);
                            case 3:
                                $passedLevel4 = $record->is_known == 1;
                                $directApproval = is_null($record->is_known) && !$record->staff->unit->leader_id;
                                return $passedLevel4 || $directApproval;
                            default:
                                return false;
                        }
                    })
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $user = Auth::user();
                        $user->staff_id = $user->staff_id ?? 1;

                        if ($user->staff->chair->level == 4){
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
                            ->body('Lembur Anda untuk ' . Carbon::parse($record->overtime_date)->translatedFormat('d F Y') . ' telah diketahui oleh ' . $user->staff->chair->level == 4 ? 'Kepala Unit' : 'Koordinator')
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
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(function ($record) {
                        $user = Auth::user();
                        if ($user->role_id == 1) {
                            return false;
                        }
                        if (!$user->staff || !$user->staff->chair) {
                            return false;
                        }
                        $userLevel = $user->staff->chair->level;
                        switch ($userLevel) {
                            case 4:
                                return is_null($record->is_known);
                            case 3:
                                $passedLevel4 = $record->is_known == 1;
                                $directApproval = is_null($record->is_known) && !$record->staff->unit->leader_id;
                                return $passedLevel4 || $directApproval;
                            default:
                                return false;
                        }
                    })
                    ->schema([
                        Textarea::make('note')
                            ->label('Alasan')
                            ->required()
                            ->rows(3),
                    ])
                    ->requiresConfirmation()
                    ->action(function ($data, $record) {
                        $user = Auth::user();
                        $user->staff_id = $user->staff_id ?? 1;

                        $record->update([
                            'is_known' => 0,
                            'known_by' => $user->staff_id,
                            'known_at' => Carbon::now(),
                            'note' => $data['note'],
                        ]);

                        Notification::make()
                            ->title('Pengajuan Lembur Ditolak oleh ' . $user->staff->chair->level == 4 ? 'Kepala Unit' : 'Koordinator')
                            ->body('Lembur Anda untuk ' . Carbon::parse($record->overtime_date)->translatedFormat('d F Y') . ' telah ditolak dengan alasan: ' . $data['note'])
                            ->success()
                            ->actions([
                                Action::make('read')
                                    ->button()
                                    ->url(OvertimeResource::getUrl('index'))
                                    ->markAsRead()
                            ])
                            ->sendToDatabase($record->staff->user);

                        Notification::make()
                            ->title('Lembur ditolak')
                            ->success()
                            ->send();
                    }),
                Action::make('verification')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check')
                    ->color('info')
                    ->visible(fn ($record) => 
                        is_null($record->is_verified) && 
                        Auth::user()->staff->chair->level == 4 &&
                        Auth::user()->role_id == 1)
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
                Action::make('cancel')
                    ->label('Batalkan')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn ($record) => 
                        is_null($record->is_verified) && 
                        Auth::user()->staff->chair->level == 4 &&
                        Auth::user()->role_id == 1)
                    ->schema([
                        Textarea::make('note')
                            ->label('Alasan')
                            ->required()
                            ->rows(3),
                    ])
                    ->requiresConfirmation()
                    ->action(function ($data, $record) {
                        $recipient = $record->staff->user;

                        $record->update([
                            'is_verified' => 0,
                            'verified_by' => Auth::user()->staff_id,
                            'verified_at' => Carbon::now(),
                            'note' => $data['note'],
                        ]);

                        Notification::make()
                            ->title('Pengajuan Lembur Ditolak SDM')
                            ->body('Lembur Anda untuk ' . Carbon::parse($record->overtime_date)->translatedFormat('d F Y') . ' telah ditolak SDM dengan alasan: ' . $data['note'])
                            ->success()
                            ->actions([
                                Action::make('read')
                                    ->button()
                                    ->url(OvertimeResource::getUrl('index'))
                                    ->markAsRead()
                            ])
                            ->sendToDatabase($recipient);

                        Notification::make()
                            ->title('Lembur dibatalkan')
                            ->success()
                            ->send();
                    }),
                ViewAction::make()
                    ->label('Lihat')
                    ->modalHeading('Detail Lembur')
                    ->schema(fn (Schema $schema) => OvertimeInfolist::configure($schema)),
            ])
            ->recordAction('view')
            ->toolbarActions([
                BulkActionGroup::make([
                    // BulkAction::make('ketahui')
                    //     ->label('Ketahui')
                    //     ->color('success')
                    //     ->requiresConfirmation()
                    //     ->visible(fn () => Auth::user()->staff->chair->level == 3 || (Auth::user()->staff->chair->level == 4 && Auth::user()->staff->unit->leader_id == Auth::user()->staff->chair_id))
                    //     ->disabled(fn (Collection $records) => !$records->doesntContain('is_known', 1) || !$records->doesntContain('is_known', 2))
                    //     ->action(function ($records) {
                    //         $user = Auth::user();
        
                    //         foreach ($records as $record) {
                    //             $user->staff_id = $user->staff_id ?? 1;

                    //             if ($user->staff->chair->level == 4){
                    //                 $record->update([
                    //                     'is_known' => 1,
                    //                 ]);
                    //             } else {
                    //                 $record->update([
                    //                     'is_known' => 2,
                    //                 ]);
                    //             }

                    //             $record->update([
                    //                 'known_by' => $user->staff_id,
                    //                 'known_at' => Carbon::now()
                    //             ]);

                    //             Notification::make()
                    //                 ->title('Pengajuan Lembur Diketahui')
                    //                 ->body('Lembur Anda untuk tanggal ' . Carbon::parse($record->overtime_date)->translatedFormat('d F Y') . ' telah diketahui oleh ' . $user->staff->chair->level == 4 ? 'Kepala Unit' : 'Koordinator')
                    //                 ->success()
                    //                 ->actions([
                    //                     Action::make('read')
                    //                         ->button()
                    //                         ->url(OvertimeResource::getUrl('index'))
                    //                         ->markAsRead()
                    //                 ])
                    //                 ->sendToDatabase($record->staff->user);
                    //         }

                    //         Notification::make()
                    //             ->title('Data lembur diketahui.')
                    //             ->success()
                    //             ->send();
                    //     }),

                    // BulkAction::make('verifikasi')
                    //     ->label('Verifikasi ')
                    //     ->color('info')
                    //     ->requiresConfirmation()
                    //     ->visible(fn () => Auth::user()->role_id == 1 && Auth::user()->staff->chair->level == 4)
                    //     ->disabled(fn (Collection $records) => !$records->doesntContain('is_verified', 1))
                    //     ->action(function ($records) {
                    //         foreach ($records as $record) {
                    //             $record->update([
                    //                 'is_verified' => 1,
                    //                 'verified_by' => Auth::user()->staff_id,
                    //                 'verified_at' => Carbon::now()
                    //             ]);

                    //             Notification::make()
                    //                 ->title('Pengajuan Lembur Diverifikasi')
                    //                 ->body('Lembur Anda untuk tanggal ' . Carbon::parse($records[0]->overtime_date)->translatedFormat('d F Y') . ' telah diverifikasi SDM')
                    //                 ->success()
                    //                 ->actions([
                    //                     Action::make('read')
                    //                         ->button()
                    //                         ->url(OvertimeResource::getUrl('index'))
                    //                         ->markAsRead()
                    //                 ])
                    //                 ->sendToDatabase($record->staff->user);
                    //         }

                    //         Notification::make()
                    //             ->title(count($records) . ' Data lembur diverifikasi.')
                    //             ->success()
                    //             ->send();
                    //     }),
                ]),
            ]);
    }
}
