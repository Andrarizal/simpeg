<?php

namespace App\Filament\Resources\Leaves\Tables;

use App\Models\Leave;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ApproveTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $query = Leave::query();

                $query->whereHas('staff.chair', function ($q) {
                    $q->where('level', '>', Auth::user()->staff->chair->level); // ambil cuti bawahan
                })
                // Jika ingin menerapkan leader unit yang sama
                // ->whereHas('staff.unit', function ($q) {
                //     $q->where('leader_id', Auth::user()->staff->unit->leader_id);
                // }) 
                ->orderBy('start_date', 'DESC');

                return $query;
            })
            ->columns([
                TextColumn::make('staff.name')
                    ->label('Nama Pengaju')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Jenis'),
                TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date(),
                TextColumn::make('end_date')
                    ->label('Selesai')
                    ->date(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Menunggu' => 'warning',
                        'Disetujui Koordinator' => 'info',
                        'Disetujui Kasi' => 'success',
                        'Disetujui Direktur' => 'success',
                        'Ditolak' => 'danger',
                    }),
                TextColumn::make('remaining')
                    ->label('Sisa Cuti')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('replacement.name')
                    ->label('Nama Pengganti')
                    ->sortable(),
                TextColumn::make('approver.name')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('adverb')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')->options([
                    'Cuti' => 'Cuti',
                    'Izin' => 'Izin',
                ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => shouldShowApprovalButton($record)) // Pakai helpers custom untuk atur visibilitas antar role
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $user = Auth::user();
                        $user->staff_id = $user->staff_id ?? 1;

                        // Cek level jabatan dari user login
                        switch ($user->staff->chair->level) {
                            case 3:
                                if ($record->status === 'Menunggu') {
                                    $record->update([
                                        'status' => 'Disetujui Koordinator',
                                        'approver_id' => $user->staff_id
                                    ]);
                                }
                                break;

                            case 2:
                                if ($record->status === 'Menunggu' && $record->staff->chair->level === 3) {
                                    $record->update([
                                        'status' => 'Disetujui Kasi',
                                        'approver_id' => $user->staff_id
                                    ]);
                                } else if ($record->status === 'Disetujui Koordinator') {
                                    $record->update([
                                        'status' => 'Disetujui Kasi',
                                        'approver_id' => $user->staff_id
                                    ]);
                                }
                                break;

                            case 1:
                                if ($record->status === 'Menunggu' && $record->staff->chair->level === 2) {
                                    $record->update([
                                        'status' => 'Disetujui Direktur',
                                        'approver_id' => $user->staff_id
                                    ]);
                                } else if (in_array($record->status, ['Disetujui Kasi'])) {
                                    $record->update([
                                        'status' => 'Disetujui Direktur',
                                        'approver_id' => $user->staff_id
                                    ]);
                                }
                                break;
                        }
                    }),
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn ($record) => shouldShowApprovalButton($record)) // Pakai helpers custom untuk atur visibilitas antar role
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $user = Auth::user();
                        $user->staff_id = $user->staff_id ?? 1;

                        // Cek level jabatan dari user login
                        switch ($user->staff->chair->level) {
                            case 3:
                                if ($record->status === 'Menunggu') {
                                    $record->update([
                                        'status' => 'Ditolak',
                                        'approver_id' => $user->staff_id
                                    ]);
                                }
                                break;

                            case 2:
                                if ($record->status === 'Menunggu' && $record->staff->chair->level === 3) {
                                    $record->update([
                                        'status' => 'Ditolak',
                                        'approver_id' => $user->staff_id
                                    ]);
                                } else if ($record->status === 'Disetujui Koordinator') {
                                    $record->update([
                                        'status' => 'Ditolak',
                                        'approver_id' => $user->staff_id
                                    ]);
                                }
                                break;

                            case 1:
                                if ($record->status === 'Menunggu' && $record->staff->chair->level === 2) {
                                    $record->update([
                                        'status' => 'Ditolak',
                                        'approver_id' => $user->staff_id
                                    ]);
                                } else if (in_array($record->status, ['Disetujui Kasi'])) {
                                    $record->update([
                                        'status' => 'Ditolak',
                                        'approver_id' => $user->staff_id
                                    ]);
                                }
                                break;
                        }
                    }),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // BulkAction::make('approve')
                    //     ->label('Setujui')
                    //     ->icon('heroicon-o-check')
                    //     ->color('success')
                    //     ->visible(fn ($record) => $record->status === 'Menunggu')
                    //     ->requiresConfirmation()
                    //     ->action(function ($record) {
                    //         $record->update(['status' => 'Disetujui']);
                    //     }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
