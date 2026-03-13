<?php

namespace App\Filament\Resources\Leaves\Pages;

use App\Filament\Resources\Leaves\LeaveResource;
use App\Models\Staff;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewLeave extends ViewRecord
{
    protected static string $resource = LeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('available')
                ->label('Bersedia')
                ->icon('heroicon-o-check')
                ->color('success')
                ->visible(fn ($record) => 
                    $record->staff_id != Auth::user()->staff_id &&
                    !$record->is_replaced &&
                    $record->status !== 'Ditolak' &&
                    is_null($record->is_verified))
                ->requiresConfirmation()
                ->action(function ($record) {
                    $record->update([
                        'is_replaced' => 1,
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

                    $head = null;
                    if ($record->staff->chair->level == 4){
                        $head = $record->staff->unit->leader->staff->first() ?? $record->staff->chair->parent->staff->first();
                    } else {
                        $head = $record->staff->chair->parent->staff->first();
                    }

                    Notification::make()
                        ->title("{$record->type} menunggu persetujuan")
                        ->body("{$record->staff->name} telah mengajukan {$record->type} pada tanggal " . Carbon::parse($record->start_date)->translatedFormat('d F Y'))
                        ->warning()
                        ->actions([
                            Action::make('review')
                                ->label('Tinjau')
                                ->url(LeaveResource::getUrl('view', ['record' => $record]))
                                ->markAsRead(),
                        ])
                        ->sendToDatabase($head->user);

                    Notification::make()
                        ->title('Berhasil menyetujui ketersediaan')
                        ->success()
                        ->send();
                }),
            Action::make('unavailable')
                ->label('Tolak')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->visible(fn ($record) => 
                    $record->staff_id != Auth::user()->staff_id &&
                    !$record->is_replaced &&
                    $record->status !== 'Ditolak' &&
                    is_null($record->is_verified))
                ->requiresConfirmation()
                ->action(function ($record) {
                    $record->update([
                        'is_replaced' => 0,
                        'replacement_at' => Carbon::now()
                    ]);

                    Notification::make()
                        ->title('Pengganti ' . $record->type . ' Anda tidak bersedia')
                        ->body('Pengganti Anda telah menyatakan ketidaksediaannya pada ' . $record->type . ' Anda tanggal ' . Carbon::parse($record->start_date)->translatedFormat('d F Y'))
                        ->warning()
                        ->actions([
                            Action::make('read')
                                ->button()
                                ->url(LeaveResource::getUrl('index'))
                                ->markAsRead()
                        ])
                        ->sendToDatabase($record->staff->user);

                    Notification::make()
                        ->title('Berhasil menolak ketersediaan')
                        ->success()
                        ->send();
                }),
            Action::make('approve')
                ->label(fn ($record) => Auth::user()->staff->chair->level > 2 || (Auth::user()->staff->chair->level == 2 && $record->staff->chair->level == 3) ? 'Ketahui' : 'Setujui')
                ->icon('heroicon-o-check')
                ->visible(fn ($record) => shouldShowApprovalButton($record))
                ->requiresConfirmation()
                ->schema([
                    Textarea::make('adverb')
                        ->label('Alasan')
                        ->rows(3),
                ])
                ->action(function (array $data, $record) {
                    $user = Auth::user();
                    $user->staff_id = $user->staff_id ?? 1;
                    $staff = $user->staff;

                    $level = $staff->chair->level;

                    $role = '';
                    $verb = '';
                    $notifColor = 'success';

                    switch ($level) {
                        case 4:
                            $role = 'Kepala Unit';
                            $verb = 'diketahui';
                            $notifColor = 'info';
                            break;
                        case 3:
                            $role = 'Koordinator';
                            $verb = 'diketahui';
                            $notifColor = 'info';
                            break;
                        case 2:
                            $role = 'Kepala Seksi';
                            $verb = 'disetujui';
                            $notifColor = 'success';
                            break;
                        case 1:
                            $role = 'Direktur';
                            $verb = 'disetujui';
                            $notifColor = 'success';
                            break;
                    }

                    if (!empty($role)) {
                        $updateData = [
                            'status'      => ucfirst($verb) . ' ' . $role,
                            'approver_id' => $staff->id,
                            'approve_at'  => Carbon::now(),
                            'adverb'      => $data['adverb']
                        ];

                        if ($level == 3 || $level == 4) {
                            $updateData['known_by'] = $staff->id;
                            $updateData['known_at'] = Carbon::now();
                        } 
                        elseif ($level == 2 && $record->staff->chair->level == 3) {
                            $updateData['known_by'] = $staff->id;
                            $updateData['known_at'] = Carbon::now();
                        }

                        $record->update($updateData);

                        Notification::make()
                            ->title($record->type . ' Anda telah ' . $verb . ' ' . $role)
                            ->body($record->type . ' Anda untuk tanggal ' . Carbon::parse($record->start_date)->translatedFormat('d F Y') . ' telah ' . $verb . ' ' . $role)
                            ->status($notifColor)
                            ->actions([
                                Action::make('read')
                                    ->button()
                                    ->url(LeaveResource::getUrl('index'))
                                    ->markAsRead(),
                            ])
                            ->sendToDatabase($record->staff->user);

                        $head = ($level == 1 || ($record->staff->chair->level == 4 && $level == 2))
                            ? Staff::whereHas('chair', fn ($q) => $q->where('name', 'like', '%SDM%'))->first()
                            : $staff->chair->parent->staff->first();

                        Notification::make()
                            ->title("{$record->type} menunggu " . str_contains($head->chair->name, 'SDM') ? 'Verifikasi' : 'Persetujuan')
                            ->body("{$record->staff->name} telah mengajukan {$record->type} pada tanggal " . Carbon::parse($record->start_date)->translatedFormat('d F Y'))
                            ->warning()
                            ->actions([
                                Action::make('review')
                                    ->label('Tinjau')
                                    ->url(LeaveResource::getUrl('view', ['record' => $record]))
                                    ->markAsRead(),
                            ])
                            ->sendToDatabase($head->user);

                        Notification::make()
                            ->title($record->type . ' ' . ucfirst($verb))
                            ->success()
                            ->send();
                    }
                }),
            Action::make('reject')
                ->label('Tolak')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->visible(fn ($record) => shouldShowApprovalButton($record))
                ->requiresConfirmation()
                ->schema([
                    Textarea::make('adverb')
                        ->label('Alasan')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data, $record) {
                    $user = Auth::user();
                    $user->staff_id = $user->staff_id ?? 1;
                    $staff = $user->staff;

                    $record->update([
                        'status' => 'Ditolak',
                        'approver_id' => $staff->id,
                        'approve_at' => Carbon::now(),
                        'adverb' => $data['adverb']
                    ]);

                    $level = $staff->chair->level;

                    $role = '';

                    switch ($level) {
                        case 4:
                            $role = 'Kepala Unit';
                            break;
                        case 3:
                            $role = 'Koordinator';
                            break;
                        case 2:
                            $role = 'Kepala Seksi';
                            break;
                        case 1:
                            $role = 'Direktur';
                            break;
                    }

                    Notification::make()
                        ->title($record->type . ' Anda telah ditolak oleh ' . $role)
                        ->body($record->type . ' Anda untuk tanggal ' . Carbon::parse($record->start_date)->translatedFormat('d F Y') . ' telah ditolak dengan alasan: ' . $data['adverb'])
                        ->danger()
                        ->actions([
                            Action::make('read')
                                ->button()
                                ->url(LeaveResource::getUrl('index'))
                                ->markAsRead()
                        ])
                        ->sendToDatabase($record->staff->user);

                    Notification::make()
                        ->title($record->type . ' ditolak')
                        ->success()
                        ->send();
                }),
            Action::make('verified')
                ->label('Verifikasi')
                ->icon('heroicon-o-check')
                ->color('info')
                ->visible(function ($record) {
                    return Auth::user()->role_id == 1 
                        && is_null($record->is_verified)
                        && ($record->staff->chair->level == 4 ? $record->status == 'Disetujui Kepala Seksi' : $record->status == 'Disetujui Direktur')
                        && $record->status != 'Ditolak'
                        && $record->is_replaced != 0;
                    })
                ->requiresConfirmation()
                ->action(function ($record) {
                    $record->update([
                        'is_verified' => 1,
                        'verified_by' => Auth::user()->staff_id,
                        'verified_at' => Carbon::now()
                    ]);

                    Notification::make()
                        ->title($record->type . ' Anda telah diverifikasi SDM')
                        ->body($record->type . ' Anda untuk tanggal ' . Carbon::parse($record->start_date)->translatedFormat('d F Y') . ' telah diverifikasi SDM')
                        ->success()
                        ->actions([
                            Action::make('read')
                                ->button()
                                ->url(LeaveResource::getUrl('index'))
                                ->markAsRead()
                        ])
                        ->sendToDatabase($record->staff->user);

                    Notification::make()
                        ->title($record->type . ' diverifikasi')
                        ->success()
                        ->send();
                }),
            Action::make('cancel')
                ->label('Batalkan')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->visible(function ($record) {
                    return Auth::user()->role_id == 1 
                        && is_null($record->is_verified)
                        && ($record->staff->chair->level == 4 ? $record->status == 'Disetujui Kepala Seksi' : $record->status == 'Disetujui Direktur')
                        && $record->status != 'Ditolak'
                        && $record->is_replaced != 0;
                })
                ->requiresConfirmation()
                ->schema([
                    Textarea::make('adverb')
                        ->label('Alasan')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data, $record) {
                    $record->update([
                        'is_verified' => 0,
                        'adverb' => $data['adverb']
                    ]);

                    Notification::make()
                        ->title($record->type . ' Anda telah dibatalkan SDM')
                        ->body($record->type . ' Anda untuk tanggal ' . Carbon::parse($record->start_date)->translatedFormat('d F Y') . ' telah dibatalkan SDM dengan alasan: ' . $data['adverb'])
                        ->danger()
                        ->actions([
                            Action::make('read')
                                ->button()
                                ->url(LeaveResource::getUrl('index'))
                                ->markAsRead()
                        ])
                        ->sendToDatabase($record->staff->user);

                    Notification::make()
                        ->title($record->type . ' dibatalkan')
                        ->success()
                        ->send();
                }),
        ];
    }
}
