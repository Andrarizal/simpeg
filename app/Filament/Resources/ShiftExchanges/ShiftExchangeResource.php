<?php

namespace App\Filament\Resources\ShiftExchanges;

use App\Filament\Resources\Schedules\ScheduleResource;
use App\Filament\Resources\ShiftExchanges\Pages\ManageShiftExchanges;
use App\Models\Schedule;
use App\Models\ShiftExchange;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use UnitEnum;

class ShiftExchangeResource extends Resource
{
    protected static ?string $model = ShiftExchange::class;

    protected static ?string $modelLabel = 'Tukar Jadwal';
    protected static ?string $pluralModelLabel = 'Permohonan Tukar Jadwal'; 
    protected static ?string $navigationLabel = 'Tukar Jadwal';
    protected static ?int $navigationSort = 4;
    protected static UnitEnum|string|null $navigationGroup = 'Jadwal';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowPathRoundedSquare;

    protected static ?string $recordTitleAttribute = 'ShiftExchange';

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('exchange_date')
                                ->label('Tanggal Pertukaran')
                                ->date('d F Y')
                                ->icon('heroicon-m-calendar-days')
                                ->weight(FontWeight::SemiBold),

                            TextEntry::make('status')
                                ->label('Persetujuan Atasan')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'Disetujui' => 'success',
                                    'Ditolak' => 'danger',
                                    'Menunggu' => 'warning',
                                }),

                            TextEntry::make('created_at')
                                ->label('Diajukan Pada')
                                ->dateTime('d M Y, H:i')
                                ->color('gray'),
                        ]),
                    ]),

                Section::make('Detail Pertukaran')
                    ->extraAttributes([
                        'class' => implode(' ', [
                            '[&_.fi-section-header]:bg-gradient-to-br',
                            '[&_.fi-section-header]:from-emerald-500',
                            '[&_.fi-section-header]:to-teal-600',
                            '[&_.fi-section-header]:dark:from-emerald-900',
                            '[&_.fi-section-header]:dark:to-teal-950',
                            '[&_.fi-section-header]:rounded-t-2xl',
                            '[&_.fi-section-header-heading]:!text-white',
                            '[&_.fi-section-header-description]:!text-white/80',
                            '[&_.fi-section-header_.fi-icon-btn]:!text-white',
                        ])
                    ])
                    ->schema([
                        Grid::make(['default' => 1, 'md' => 11]) 
                            ->extraAttributes([
                                'class' => '[&_.fi-in-entry]:h-full'
                            ])
                            ->schema([
                                Group::make([
                                    TextEntry::make('staff.name')
                                        ->label('Pihak 1 (Pemohon)')
                                        ->icon('heroicon-m-user-circle')
                                        ->weight(FontWeight::Bold)
                                        ->extraAttributes(['class' => '-mt-2'])
                                        ->color('primary'),
                                    
                                    TextEntry::make('staffSchedule.shift.name')
                                        ->label('Jadwal Asli')
                                        ->badge()
                                        ->inlineLabel()
                                        ->alignEnd()
                                        ->color('info'),
                                ])
                                ->extraAttributes(['class' => 'bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700'])
                                ->columnSpan(['md' => 5]),

                                TextEntry::make('separator')
                                    ->hiddenLabel()
                                    ->state(new HtmlString(
                                        Blade::render('<div class="flex items-center justify-center h-full text-gray-400">
                                            <x-heroicon-m-arrows-right-left class="w-5" />
                                        </div>')
                                    ))
                                    ->columnSpan(['md' => 1]),

                                Group::make([
                                    TextEntry::make('replacer.name')
                                        ->label('Pihak 2 (Pengganti)')
                                        ->icon('heroicon-m-user-circle')
                                        ->weight(FontWeight::Bold)
                                        ->extraAttributes(['class' => '-mt-2'])
                                        ->color('primary'),

                                    TextEntry::make('replacerSchedule.shift.name')
                                        ->label('Jadwal Asli')
                                        ->badge()
                                        ->inlineLabel()
                                        ->alignEnd()
                                        ->color('warning'),
                                ])
                                ->extraAttributes(['class' => 'bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700'])
                                ->columnSpan(['md' => 5]),
                            ]),

                        TextEntry::make('reason')
                            ->label('Alasan Penukaran')
                            ->columnSpanFull()
                            ->extraAttributes([
                                'class' => '-mt-2'
                            ])
                            ->prose(), 
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $staff = Auth::user()->staff;

                if (!$staff) return $query;

                if ($staff->chair && $staff->chair->level == 4) {
                    $query->whereHas('staff', function (Builder $q) use ($staff) {
                        $q->where('unit_id', $staff->unit_id);
                    });
                } else {
                    $query->whereHas('staff', function (Builder $staffQuery) use ($staff) {
                        $staffQuery->where(function (Builder $subQ) use ($staff) {
                            $subQ->whereHas('unit', function (Builder $unitQ) {
                                $unitQ->whereNull('leader_id');
                            })
                            ->whereHas('chair', function (Builder $chairQ) use ($staff) {
                                $chairQ->where('head_id', $staff->chair_id);
                            });
                        });
                    });
                }
            })
            ->recordTitleAttribute('ShiftExchange')
            ->columns([
                TextColumn::make('exchange_date')
                    ->label('Tanggal')
                    ->date('d F Y')
                    ->sortable(),
                TextColumn::make('staff.name')
                    ->label('Nama Penukar')
                    ->formatStateUsing(fn ($record) => $record->staff->name.' ('.$record->staffSchedule->shift->name.')' )
                    ->sortable(),
                TextColumn::make('replacer.name')
                    ->label('Tukar dengan')
                    ->formatStateUsing(fn ($record) => $record->replacer->name.' ('.$record->replacerSchedule->shift->name.')' )
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->alignCenter()
                    ->color(function ($state) {
                        if (str_contains($state, 'Disetujui')) {
                            return 'success';
                        } else if (str_contains($state, 'Ditolak')) {
                            return 'danger';
                        } else {
                            return 'warning';
                        }
                    }),
            ])
            ->filters([
                SelectFilter::make('month_year')
                        ->label('Bulan')
                        ->options(
                            collect(range(0, 11))
                                ->mapWithKeys(fn ($i) => [
                                    now()->subMonths($i)->format('Y-m') =>
                                        now()->subMonths($i)->translatedFormat('F Y'),
                                ])
                        )
                        ->default(now()->format('Y-m'))
                        ->query(function (Builder $query, array $data) {
                            if (empty($data['value'])) return;

                            $date = Carbon::createFromFormat('Y-m', $data['value']);

                            $query->whereMonth('exchange_date', $date->month)
                                ->whereYear('exchange_date', $date->year);
                        })
                        ->indicateUsing(function (array $data) {
                            if (empty($data['value'])) return [];

                            return [
                                Indicator::make('Bulan: ' . Carbon::parse($data['value'])->translatedFormat('F Y')),
                            ];
                        })
                        ->selectablePlaceholder(false)
                        ->native(false)
            ])
            ->recordActions([
                Action::make('approved')
                    ->label('Setujui')
                    ->color('info')
                    ->icon('heroicon-m-check-circle')
                    ->visible(fn (ShiftExchange $record) => $record->status == 'Menunggu')
                    ->requiresConfirmation()
                    ->action(function (ShiftExchange $record) {
                        $record->update([
                            'status' => 'Disetujui',
                            'approved_by' => Auth::user()->staff_id,
                            'approved_at' => now(),
                        ]);

                        $scheduleOwner = Schedule::find($record->staff_schedule_id);
                        $scheduleReplacer = Schedule::find($record->replacer_schedule_id);

                        $ownerShiftId = $scheduleOwner->shift_id;
                        $replacerShiftId = $scheduleReplacer->shift_id;

                        $scheduleOwner->update([
                            'shift_id' => $replacerShiftId
                        ]);

                        $scheduleReplacer->update([
                            'shift_id' => $ownerShiftId
                        ]);

                        Notification::make()
                            ->title('Permohonan tukar jadwal telah disetujui.')
                            ->success()
                            ->send();

                        Notification::make()
                            ->title('Permohonan tukar jadwal Anda telah disetujui.')
                            ->body('Permohonan tukar jadwal Anda untuk tanggal ' . Carbon::parse($record->exchange_date)->translatedFormat('d F Y') . ' telah disetujui')
                            ->success()
                            ->actions([
                                Action::make('read')
                                    ->label('Lihat Jadwal')
                                    ->button()
                                    ->url(ScheduleResource::getUrl('index'))
                                    ->markAsRead()
                            ])
                            ->sendToDatabase($record->staff->user);

                        Notification::make()
                            ->title('Jadwal Anda telah diperbarui.')
                            ->body('Jadwal Anda untuk tanggal ' . Carbon::parse($record->exchange_date)->translatedFormat('d F Y') . ' telah diperbarui oleh pembuat jadwal. Silakan cek jadwal Anda untuk melihat perubahan.')
                            ->success()
                            ->actions([
                                Action::make('read')
                                    ->label('Lihat Jadwal')
                                    ->button()
                                    ->url(ScheduleResource::getUrl('index'))
                                    ->markAsRead()
                            ])
                            ->sendToDatabase($record->replacer->user);
                    }),
                Action::make('rejected')
                    ->label('Tolak')
                    ->color('danger')
                    ->icon('heroicon-m-x-circle')
                    ->visible(fn (ShiftExchange $record) => $record->status == 'Menunggu')
                    ->requiresConfirmation()
                    ->action(function (ShiftExchange $record) {
                        $record->update([
                            'status' => 'Ditolak',
                            'approved_by' => Auth::user()->staff_id,
                            'approved_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Permohonan tukar jadwal telah ditolak.')
                            ->success()
                            ->send();

                        Notification::make()
                            ->title('Permohonan tukar jadwal Anda telah ditolak.')
                            ->body('Permohonan tukar jadwal Anda untuk tanggal ' . Carbon::parse($record->exchange_date)->translatedFormat('d F Y') . ' telah ditolak')
                            ->danger()
                            ->actions([
                                Action::make('read')
                                    ->label('Lihat Jadwal')
                                    ->button()
                                    ->url(ScheduleResource::getUrl('index'))
                                    ->markAsRead()
                            ])
                            ->sendToDatabase($record->staff->user);
                    }),
                ViewAction::make()
                    ->modalHeading('Detail Permohonan Tukar Jadwal')
                    ->modalWidth('3xl'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageShiftExchanges::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        $staff = Auth::user()->staff;

        return $staff->chair->level == 3 || ($staff->chair->level == 4 && $staff->unit->leader_id == $staff->chair_id);
    }
}
