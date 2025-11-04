<?php

namespace App\Filament\Resources\Leaves\Tables;

use App\Models\Leave;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
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
                $query->where('staff_id', '!=', Auth::user()->staff_id);
                if (Auth::user()->role_id == 1){
                    $query->orderBy('created_at', 'DESC');
                } else {
                    $query->whereHas('staff', function ($q) {
                            $q->where('unit_id', Auth::user()->staff->unit_id);
                        })
                        ->whereHas('staff.chair', function ($q) {
                            if (Auth::user()->staff->unit->leader_id == Auth::user()->staff->id){
                                $q->where('level', Auth::user()->staff->chair->level);
                                // ambil cuti bawah kepala unit
                            } else {
                                $q->where('level', '>', Auth::user()->staff->chair->level); // ambil cuti bawahan
                            }
                        })
                        ->orderBy('created_at', 'DESC');
                }
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
                    ->alignCenter()
                    ->color(fn (string $state): string => match ($state) {
                        'Menunggu' => 'warning',
                        'Diketahui Kepala Unit' => 'info',
                        'Diketahui Koordinator' => 'primary',
                        'Disetujui Kasi' => 'success',
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
                TextColumn::make('remaining')
                    ->label('Sisa Cuti')
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
                    ->label(fn() => Auth::user()->staff->chair->level > 2 ? 'Ketahui' : 'Setujui')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => shouldShowApprovalButton($record)) // Pakai helpers custom untuk atur visibilitas antar role
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $user = Auth::user();
                        $user->staff_id = $user->staff_id ?? 1;

                        // Cek level jabatan dari user login
                        switch ($user->staff->chair->level) {
                            case 4:
                                $record->update([
                                    'status' => 'Diketahui Kepala Unit',
                                    'approver_id' => $user->staff_id
                                ]);
                                break;
                            case 3:
                                $record->update([
                                    'status' => 'Diketahui Koordinator',
                                    'approver_id' => $user->staff_id
                                ]);
                                break;
                            case 2:
                                $record->update([
                                    'status' => 'Disetujui Kepala Seksi',
                                    'approver_id' => $user->staff_id
                                ]);
                                break;
                            case 1:
                                $record->update([
                                    'status' => 'Disetujui Direktur',
                                    'approver_id' => $user->staff_id
                                ]);
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

                        $record->update([
                            'status' => 'Ditolak',
                            'approver_id' => $user->staff_id
                        ]);
                    }),
                Action::make('verified')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check')
                    ->color('info')
                    ->visible(function ($record) {
                        if (Auth::user()->role_id === 1) {
                            return $record->is_verified ? false : true;
                        }
                        return false;
                    })
                    // ->visible(true)
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'is_verified' => 1,
                        ]);
                    }),
                Action::make('cancel')
                    ->label('Batalkan')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(function ($record) {
                        if (Auth::user()->role_id === 1) {
                            return $record->is_verified ? false : true;
                        }
                        return false;
                    })
                    // ->visible(true)
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'is_verified' => 0,
                        ]);
                    }),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                DeleteBulkAction::make(),
                ]),
            ]);
    }
}
