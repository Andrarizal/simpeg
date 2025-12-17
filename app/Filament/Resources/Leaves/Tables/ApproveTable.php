<?php

namespace App\Filament\Resources\Leaves\Tables;

use App\Filament\Resources\Leaves\LeaveResource;
use App\Models\Chair;
use App\Models\Leave;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
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
                $query->where('staff_id', '!=', Auth::user()->staff_id); // Buang cuti milik sendiri

                // Jika SDM
                if (Auth::user()->role_id == 1){
                    $query->orderBy('created_at', 'DESC');
                // JIka Bukan SDM
                } else {
                    // Jika Kanit
                    if (Auth::user()->staff->chair->level == 4){
                        $query->whereHas('staff.chair', function ($q) {
                            // Ambil yang satu struktur kepengurusan (Koor User Cuti == Koor Kanit)
                            $q->where('head_id', Auth::user()->staff->chair->head_id);
                            // Ambil yang selevel (Karyawan)
                            $q->where('level', Auth::user()->staff->chair->level);
                        });
                    // Jika lebih tinggi dari Kanit
                    } else if (Auth::user()->staff->chair->level != 1) {
                        // Masukkan id dari atasan (langsung) user cuti ke array heads
                        $heads = Leave::with(['staff.chair', 'approver.chair'])
                                ->get()
                                ->map(function ($leave) {
                                    return [$leave->staff->chair->head_id];
                                })
                                ->toArray();
                                
                        foreach($heads as &$head){
                            // Cek apabila atasan yang ada di head bukan direktur
                            while (!in_array(null, $head)){
                                // Kumpulkan semua atasan dari user cuti
                                $head[] = Chair::where('id', end($head))->first()->head_id;
                            }
                        }
                        unset($head);
                        
                        $matchFound = false;
                        foreach ($heads as $head){
                            // Jika terdapat user login yang sesuai dengan salah satu heads
                            if(in_array(Auth::user()->staff->chair_id, $head)){
                                $matchFound = true;
                                // Ambil yang memiliki level di bawahnya
                                $query->whereHas('staff.chair', function ($q) use ($head){
                                    $q->whereIn('head_id', $head)
                                    ->where('level', '>', Auth::user()->staff->chair->level);
                                });
                                break;
                            }
                        }

                        // Jika User login tidak sesuai dengan heads
                        if (!$matchFound) {
                            $query->whereRaw('1 = 0'); // Paksa hasil kosong
                        }
                    }
                }
                return $query->orderBy('created_at', 'DESC');
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
                    ->label(fn ($record) => Auth::user()->staff->chair->level > 2 || (Auth::user()->staff->chair->level == 2 && $record->staff->chair->level == 3) ? 'Ketahui' : 'Setujui')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => shouldShowApprovalButton($record)) // Pakai helpers custom untuk atur visibilitas antar role
                    ->requiresConfirmation()
                    ->schema([
                        Textarea::make('adverb')
                            ->label('Catatan')
                            ->rows(3),
                    ])
                    ->action(function (array $data, $record) {
                        $user = Auth::user();
                        $user->staff_id = $user->staff_id ?? 1;

                        // Cek level jabatan dari user login
                        switch ($user->staff->chair->level) {
                            case 4:
                                $record->update([
                                    'status' => 'Diketahui Kepala Unit',
                                    'approver_id' => $user->staff_id,
                                    'approve_at' => Carbon::now(),
                                    'adverb' => $data['adverb']
                                ]);

                                Notification::make()
                                    ->title($record->type . ' Anda telah diketahui Kepala Unit')
                                    ->body($record->type . ' Anda untuk tanggal ' . Carbon::parse($record->start_date)->translatedFormat('d F Y') . ' telah diketahui Kepala Unit')
                                    ->info()
                                    ->actions([
                                        Action::make('read')
                                            ->button()
                                            ->url(LeaveResource::getUrl('index'))
                                            ->markAsRead()
                                    ])
                                    ->sendToDatabase($record->staff->user);

                                Notification::make()
                                    ->title($record->type . ' Diketahui')
                                    ->success()
                                    ->send();
                                break;
                            case 3:
                                $record->update([
                                    'status' => 'Diketahui Koordinator',
                                    'approver_id' => $user->staff_id,
                                    'approve_at' => Carbon::now(),
                                    'known_by' => $user->staff_id,
                                    'known_at' => Carbon::now(),
                                    'adverb' => $data['adverb']
                                ]);

                                Notification::make()
                                    ->title($record->type . ' Anda telah diketahui Koordinator')
                                    ->body($record->type . ' Anda untuk tanggal ' . Carbon::parse($record->start_date)->translatedFormat('d F Y') . ' telah diketahui Koordinator')
                                    ->info()
                                    ->actions([
                                        Action::make('read')
                                            ->button()
                                            ->url(LeaveResource::getUrl('index'))
                                            ->markAsRead()
                                    ])
                                    ->sendToDatabase($record->staff->user);

                                Notification::make()
                                    ->title($record->type . ' Diketahui')
                                    ->success()
                                    ->send();
                                break;
                            case 2:
                                $record->update([
                                    'status' => 'Disetujui Kepala Seksi',
                                    'approver_id' => $user->staff_id,
                                    'approve_at' => Carbon::now(),
                                    'adverb' => $data['adverb']
                                ]);

                                if ($record->staff->chair->level == 3){
                                    $record->update([
                                        'known_by' => $user->staff_id,
                                        'known_at' => Carbon::now()
                                    ]);
                                }

                                Notification::make()
                                    ->title($record->type . ' Anda telah disetujui Kepala Seksi')
                                    ->body($record->type . ' Anda untuk tanggal ' . Carbon::parse($record->start_date)->translatedFormat('d F Y') . ' telah disetujui Kepala Seksi')
                                    ->success()
                                    ->actions([
                                        Action::make('read')
                                            ->button()
                                            ->url(LeaveResource::getUrl('index'))
                                            ->markAsRead()
                                    ])
                                    ->sendToDatabase($record->staff->user);

                                Notification::make()
                                    ->title($record->type . ' Disetujui')
                                    ->success()
                                    ->send();
                                break;
                            case 1:
                                $record->update([
                                    'status' => 'Disetujui Direktur',
                                    'approver_id' => $user->staff_id,
                                    'approve_at' => Carbon::now(),
                                    'adverb' => $data['adverb']
                                ]);

                                Notification::make()
                                    ->title($record->type . ' Anda telah disetujui Direktur')
                                    ->body($record->type . ' Anda untuk tanggal ' . Carbon::parse($record->start_date)->translatedFormat('d F Y') . ' telah disetujui Direktur')
                                    ->success()
                                    ->actions([
                                        Action::make('read')
                                            ->button()
                                            ->url(LeaveResource::getUrl('index'))
                                            ->markAsRead()
                                    ])
                                    ->sendToDatabase($record->staff->user);

                                Notification::make()
                                    ->title($record->type . ' Disetujui')
                                    ->success()
                                    ->send();
                                break;
                        }

                        
                    }),
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn ($record) => shouldShowApprovalButton($record)) // Pakai helpers custom untuk atur visibilitas antar role
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

                        $record->update([
                            'status' => 'Ditolak',
                            'approver_id' => $user->staff_id,
                            'approve_at' => Carbon::now(),
                            'adverb' => $data['adverb']
                        ]);

                        Notification::make()
                            ->title($record->type . ' Anda telah ditolak')
                            ->body($record->type . ' Anda untuk tanggal ' . Carbon::parse($record->start_date)->translatedFormat('d F Y') . ' telah ditolak')
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
                        if (Auth::user()->role_id == 1) {
                            return $record->is_verified == 0 || $record->is_verified == 1 || $record->is_replaced == 0 || $record->status == 'Ditolak' ? false : true;
                        }
                        return false;
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
                        if (Auth::user()->role_id == 1) {
                            return $record->is_verified == 0 || $record->is_verified == 1 || $record->is_replaced == 0 || $record->status == 'Ditolak' ? false : true;
                        }
                        return false;
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
                            ->body($record->type . ' Anda untuk tanggal ' . Carbon::parse($record->start_date)->translatedFormat('d F Y') . ' telah dibatalkan SDM')
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
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                ]),
            ]);
    }
}
