<?php

namespace App\Filament\Resources\Overtimes\Tables;

use App\Models\Overtime;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ApproveTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $query = Overtime::query();
                $query->where('staff_id', '!=', Auth::user()->staff_id); 

                // Jika SDM
                if (Auth::user()->role_id == 1){
                    $query->orderBy('created_at', 'DESC');
                // Jika Bukan SDM
                } else {
                    // Jika Kanit
                    if (Auth::user()->staff->chair->level == 4){
                        $query->whereHas('staff.chair', function ($q) {
                            $q->where('head_id', Auth::user()->staff->chair->head_id);
                        });
                        // Jika Koor
                    } else if (Auth::user()->staff->chair->level == 3) {
                        $query->whereHas('staff.chair', function ($q) {
                            $q->where('head_id', Auth::user()->staff->chair_id);
                        });
                        $query->whereHas('staff.unit', function ($q) {
                            $q->whereColumn('staff.chair_id', 'units.leader_id');
                        });
                    }
                }
                return $query->orderBy('created_at', 'DESC');
            })
            ->columns([
                TextColumn::make('staff.name')
                    ->label('Nama Pengaju')
                    ->sortable(),
                TextColumn::make('overtime_date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('start_time')
                    ->label('Mulai')
                    ->time()
                    ->sortable(),
                TextColumn::make('end_time')
                    ->label('Selesai')
                    ->time()
                    ->sortable(),
                TextColumn::make('hours')
                    ->label('Total Jam')
                    ->formatStateUsing(fn ($state) => $state . ' Jam')
                    ->sortable(),
                TextColumn::make('reason')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->badge()
                    ->alignCenter()
                    ->color(fn ($state) => match ($state) {
                        'Menunggu' => 'warning',
                        'Disetujui' => 'success',
                        'Ditolak' => 'danger',
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
                //
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status == 'Menunggu' ? true : false)
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('adverb')
                            ->label('Catatan')
                            ->rows(3),
                    ])
                    ->action(function (array $data, $record) {
                        $user = Auth::user();
                        $user->staff_id = $user->staff_id ?? 1;

                        $record->update([
                            'status' => 'Disetujui',
                            'adverb' => $data['adverb']
                        ]);

                        Notification::make()
                            ->title('Lembur disetujui')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status == 'Menunggu' ? true : false)
                    ->requiresConfirmation()
                    ->form([
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
                            'adverb' => $data['adverb']
                        ]);

                        Notification::make()
                            ->title('Lembur ditolak')
                            ->success()
                            ->send();
                    }),
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
