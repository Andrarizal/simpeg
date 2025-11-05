<?php

namespace App\Filament\Resources\Leaves\Pages;

use App\Filament\Resources\Leaves\LeaveResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewLeave extends ViewRecord
{
    protected static string $resource = LeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label(fn ($record) => Auth::user()->staff->chair->level > 2 || (Auth::user()->staff->chair->level == 2 && $record->staff->chair->level == 3) ? 'Ketahui' : 'Setujui')
                ->icon('heroicon-o-check')
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
        ];
    }
}
