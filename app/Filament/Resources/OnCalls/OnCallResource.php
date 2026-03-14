<?php

namespace App\Filament\Resources\OnCalls;

use App\Filament\Resources\OnCalls\Pages\ManageOnCalls;
use App\Filament\Resources\Overtimes\OvertimeResource;
use App\Models\MonthlyPeriod;
use App\Models\OnCall;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class OnCallResource extends Resource
{
    protected static ?string $model = OnCall::class;

    protected static ?string $modelLabel = 'On Call';       
    protected static ?string $pluralModelLabel = 'Daftar On Call'; 
    protected static ?string $navigationLabel = 'On Call';
    protected static ?int $navigationSort = 2;
    protected static UnitEnum|string|null $navigationGroup = 'Keperluan';

    protected static ?string $recordTitleAttribute = 'OnCall';

    public static function isSubordinate(): bool
    {
        $user = Auth::user();

        if ($user->role_id == 1) {
            return false;
        }

        if (!$user || !$user->staff || !$user->staff->chair) {
            return false;
        }

        return $user->staff->chair->level == 4 
            && $user->staff->unit?->leader_id != $user->staff->chair_id;
    }

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return self::isSubordinate() 
            ? Heroicon::PhoneArrowDownLeft 
            : Heroicon::PhoneArrowUpRight;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make('Rencana Pelaksanaan')
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
                                    ->description('Detail tanggal dan uraian tugas yang akan dikerjakan.')
                                    ->icon('heroicon-m-clipboard-document-list')
                                    ->schema([
                                        DatePicker::make('oncall_date')
                                            ->label('Tanggal On Call')
                                            ->prefixIcon('heroicon-m-calendar-days')
                                            ->minDate(fn () => Carbon::today())
                                            ->maxDate(date('Y-12-31'))
                                            ->displayFormat('d F Y')
                                            ->required()
                                            ->native(false)
                                            ->columnSpanFull(),

                                        Textarea::make('command')
                                            ->label('Perintah / Uraian Tugas')
                                            ->placeholder('Jelaskan secara rinci tugas yang harus diselesaikan...')
                                            ->rows(4)
                                            ->required()
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 2]),

                        Group::make()
                            ->schema([
                                Section::make('Rincian On Call')
                                    ->icon('heroicon-m-user')
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
                                        Select::make('staff_id')
                                            ->label('Nama Pegawai')
                                            ->relationship(
                                                name: 'staff', 
                                                titleAttribute: 'name',
                                                modifyQueryUsing: function (Builder $query) {
                                                    $user = Auth::user();
                                                    $staff = $user->staff;

                                                    if (!$staff || !$staff->chair) {
                                                        return $query->whereRaw('1 = 0');
                                                    }

                                                    $chairId = $staff->chair_id;
                                                    $level = $staff->chair->level;

                                                    if ($level == 4) {
                                                        return $query->whereHas('unit', function (Builder $q) use ($chairId) {
                                                            $q->where('leader_id', $chairId);
                                                        })->where('id', '!=', $staff->id);
                                                    }

                                                    if ($level == 3) {
                                                        return $query->whereHas('chair', function (Builder $q) use ($chairId) {
                                                            $q->where('head_id', $chairId);
                                                        });
                                                    }

                                                    return $query->whereRaw('1 = 0');
                                                }
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->native(false),

                                        TimePicker::make('start_time')
                                            ->label('Waktu Mulai')
                                            ->prefixIcon('heroicon-m-play')
                                            ->native(false)
                                            ->displayFormat('H:i')
                                            ->required()
                                            ->seconds(false),

                                        TimePicker::make('end_time')
                                            ->label('Waktu Selesai')
                                            ->prefixIcon('heroicon-m-stop')
                                            ->native(false)
                                            ->displayFormat('H:i')
                                            ->required()
                                            ->seconds(false),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])
                    ->schema([
                        Group::make([
                            Section::make('Detail Pelaksanaan')
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
                                ->icon('heroicon-m-clipboard-document-list')
                                ->schema([
                                    TextEntry::make('staff.name')
                                        ->label('Nama Pegawai')
                                        ->icon('heroicon-m-user-circle')
                                        ->hiddenLabel()
                                        ->weight(FontWeight::SemiBold)
                                        ->size(TextSize::Small),

                                    TextEntry::make('command')
                                        ->label('Perintah / Uraian Tugas')
                                        ->markdown()
                                        ->prose()
                                        ->columnSpanFull()
                                        ->extraAttributes(['class' => 'bg-gray-50 dark:bg-gray-900 p-2 px-4 rounded-2xl border border-gray-200 dark:border-gray-800']),

                                    Group::make()
                                        ->extraAttributes([
                                            'class' => '[&_.fi-grid]:gap-2 [&_.fi-grid]:flex [&_.fi-grid]:flex-wrap -mt-4',
                                        ])
                                        ->schema([
                                            TextEntry::make('is_known')
                                                ->badge()
                                                ->hiddenLabel()
                                                ->default('null')
                                                ->formatStateUsing(fn ($state) => match ($state) {
                                                    2 => 'Diketahui Koordinator',
                                                    1 => 'Diketahui Kepala Unit',
                                                    0 => 'Ditolak Atasan',
                                                    'null' => 'Menunggu Diketahui Atasan',
                                                })
                                                ->color(fn ($state) => match ($state) {
                                                    1, 2 => 'success',
                                                    0 => 'danger',
                                                    'null' => 'warning',
                                                })
                                                ->icon(fn ($state) => match ($state) {
                                                    1, 2 => 'heroicon-m-check-circle',
                                                    0 => 'heroicon-m-x-circle',
                                                    'null' => 'heroicon-m-clock',
                                                }),

                                            TextEntry::make('is_verified')
                                                ->hiddenLabel()
                                                ->badge()
                                                ->default('null')
                                                ->formatStateUsing(fn ($state) => match ($state) {
                                                    1 => 'Terverifikasi SDM',
                                                    0 => 'Tidak Terverifikasi SDM',
                                                    'null' => 'Menunggu Verifikasi SDM',
                                                })
                                                ->color(fn ($state) => match ($state) {
                                                    1 => 'info',
                                                    0 => 'danger',
                                                    'null' => 'warning',
                                                })
                                                ->icon(fn ($state) => match ($state) {
                                                    1 => 'heroicon-m-shield-check',
                                                    0 => 'heroicon-m-exclamation-circle',
                                                    'null' => 'heroicon-m-clock',
                                                }),
                                        ]),
                                    TextEntry::make('note')
                                        ->label('Catatan Tambahan')
                                        ->placeholder('Tidak ada catatan')
                                        ->markdown()
                                        ->columnSpanFull(),
                                ]),
                        ])->columnSpan(['lg' => 2]),
                        Group::make([
                            Section::make('Waktu On Call')
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
                                ->icon('heroicon-m-clock')
                                ->schema([
                                    TextEntry::make('oncall_date')
                                        ->label('Tanggal')
                                        ->date('d F Y')
                                        ->icon('heroicon-m-calendar'),
                                    TextEntry::make('time_range')
                                        ->label('Jam Pelaksanaan')
                                        ->icon('heroicon-m-clock')
                                        ->state(function ($record) {
                                            $start = Carbon::parse($record->start_time)->format('H:i');
                                            $end = $record->end_time ? Carbon::parse($record->end_time)->format('H:i') : '...';
                                            return "{$start} - {$end} WIB";
                                        }),
                                    TextEntry::make('hours')
                                        ->label('Durasi Total')
                                        ->color('primary')
                                        ->weight(FontWeight::Bold)
                                        ->size(TextSize::Large)
                                        ->state(function ($record) {
                                            if (! $record || ! $record->end_time) {
                                                return 'Sedang Berjalan';
                                            }
                                            $total = $record->getTotalHours();
                                            return $total ? "{$total} Jam" : '-';
                                        })
                                        ->badge(fn ($record) => $record->end_time ? false : true)
                                        ->color(fn ($record) => $record->end_time ? 'primary' : 'warning'),
                                ]),
                        ])->columnSpan(['lg' => 1]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('OnCall')
            ->columns([
                TextColumn::make('staff.name')
                    ->label('Penerima Perintah')
                    ->visible(function ($livewire) {
                        if (self::isSubordinate()) return false;

                        $filterMode = $livewire->tableFilters['role_view']['value'] ?? null;

                        if (! $filterMode) {
                            $filterMode = Auth::user()->role_id == 1 ? 'as_verifier' : 'as_commander';
                        }
                        
                        return $filterMode === 'as_commander' || $filterMode === 'as_verifier';
                    }),

                TextColumn::make('commander.name')
                    ->label('Pemberi Perintah')
                    ->visible(function ($livewire) {
                        if (self::isSubordinate()) return true;

                        $filterMode = $livewire->tableFilters['role_view']['value'] ?? null;

                        if (! $filterMode) {
                            $filterMode = Auth::user()->role_id == 1 ? 'as_verifier' : 'as_commander';
                        }

                        return $filterMode === 'as_receiver' || $filterMode === 'as_verifier';
                    }),
                TextColumn::make('command')
                    ->label('Perintah')
                    ->wrap()
                    ->extraAttributes(['class' => 'min-w-xs']),
                TextColumn::make('oncall_date')
                    ->label('Tanggal')
                    ->date('d F Y')
                    ->sortable(),
                TextColumn::make('start_time')
                    ->label('Mulai')
                    ->time('H:i')
                    ->alignCenter(),
                TextColumn::make('end_time')
                    ->label('Selesai')
                    ->time('H:i')
                    ->alignCenter(),
                TextColumn::make('hours')
                    ->label('Total Jam')
                    ->state(function ($record) {
                        if (! $record || ! $record->end_time) {
                            return '---';
                        }

                        $total = $record->getTotalHours();
                        return $total ? "{$total} jam" : '-';
                    })
                    ->alignCenter(),
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
                SelectFilter::make('period_id')
                    ->label('Periode On Call')
                    ->options(function () {
                        return MonthlyPeriod::orderBy('start_date', 'desc')
                            ->get()
                            ->mapWithKeys(fn ($period) => [$period->id => "{$period->name}"]);
                    })
                    ->default(function () {
                        return MonthlyPeriod::where('start_date', '<=', now())
                            ->where('end_date', '>=', now())
                            ->value('id');
                    })
                    ->query(function (Builder $query, $data) {
                        $query->where('period_id', $data['value']);
                    })
                    ->indicateUsing(function ($data) {
                        if (! $data['value']) {
                            return null;
                        }
                        
                        $periodName = MonthlyPeriod::find($data['value'])?->name;
                        return [
                            Indicator::make('Periode: ' . $periodName)
                                ->removable(false),
                        ];
                    })
                    ->selectablePlaceholder(false)
                    ->native(false),
                SelectFilter::make('role_view')
                    ->label('Mode Tampilan')
                    ->options(function () {
                        $user = Auth::user();
                        if ($user->role_id == 1) {
                            return [
                                'as_verifier' => 'Verifikasi SDM',
                                'as_receiver' => 'Tugas Saya',
                            ];
                        }

                        return [
                            'as_commander' => 'Perintah Saya',
                            'as_receiver'  => 'Tugas Saya',
                        ];
                    })
                    ->default(fn () => Auth::user()->role_id == 1 ? 'as_verifier' : 'as_commander')
                    ->visible(fn () => Auth::user()->role_id == 1 || (!self::isSubordinate() && Auth::user()->staff->unit->leader_id == Auth::user()->staff->chair_id))
                    ->query(function (Builder $query, array $data) {
                        $user = Auth::user();
                        $mode = $data['value'];

                        if ($mode === 'as_commander') {
                            return $query->where('command_by', $user->staff_id);
                        }
                        
                        if ($mode === 'as_receiver') {
                            return $query->where('staff_id', $user->staff_id);
                        }

                        if ($mode === 'as_verifier') {
                            return $query; 
                        }
                    })
                    ->indicateUsing(function ($data) {
                        if (! $data['value']) return null;

                        $labels = [
                            'as_commander' => 'Perintah Saya',
                            'as_receiver'  => 'Tugas Saya',
                            'as_verifier'  => 'Verifikasi SDM',
                        ];

                        return [
                            Indicator::make('Tampilan: ' . ($labels[$data['value']] ?? '-'))
                                ->removable(false),
                        ];
                    })
                    ->native(false),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Ketahui')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(function ($record) {
                        $user = Auth::user();
                        if ($user->role_id == 1 || !$user->staff || !$user->staff->chair || $user->staff_id == $record->staff_id) return false;

                        return is_null($record->is_known) && $record->command_by == $user->staff_id;
                    })
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $user = Auth::user();
                        $user->staff_id = $user->staff_id ?? 1;

                        $record->update([
                            'is_known' => 1,
                            'known_at' => Carbon::now()
                        ]);

                        Notification::make()
                            ->title('Pengajuan On Call Diketahui')
                            ->body('Pekerjaan On Call Anda pada ' . Carbon::parse($record->overtime_date)->translatedFormat('d F Y') . ' telah diketahui oleh Atasan')
                            ->success()
                            ->actions([
                                Action::make('read')
                                    ->label('Lihat')
                                    ->button()
                                    ->url(OnCallResource::getUrl('index'))
                                    ->markAsRead()
                            ])
                            ->sendToDatabase($record->staff->user);

                        Notification::make()
                            ->title('Perintah On Call diketahui')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(function ($record) {
                        $user = Auth::user();
                        if ($user->role_id == 1 || !$user->staff || !$user->staff->chair || $user->staff_id == $record->staff_id) return false;

                        return is_null($record->is_known) && $record->command_by == $user->staff_id;
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
                            'known_at' => Carbon::now(),
                            'note' => $data['note'],
                        ]);

                        Notification::make()
                            ->title('On Call Anda ditolak oleh Atasan')
                            ->body('Pekerjaan On Call Anda untuk ' . Carbon::parse($record->overtime_date)->translatedFormat('d F Y') . ' telah ditolak dengan alasan: ' . $data['note'])
                            ->success()
                            ->actions([
                                Action::make('read')
                                    ->label('Lihat')
                                    ->button()
                                    ->url(OnCallResource::getUrl('index'))
                                    ->markAsRead()
                            ])
                            ->sendToDatabase($record->staff->user);

                        Notification::make()
                            ->title('Perintah On Call ditolak')
                            ->success()
                            ->send();
                    }),
                Action::make('verification')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check')
                    ->color('info')
                    ->visible(fn ($record, $livewire) => 
                        is_null($record->is_verified) && 
                        $livewire->tableFilters['role_view']['value'] == 'as_verifier')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $recipient = $record->staff->user;

                        $record->update([
                            'is_verified' => 1,
                            'verified_by' => Auth::user()->staff_id,
                            'verified_at' => Carbon::now()
                        ]);

                        Notification::make()
                            ->title('Pekerjaan On Call Diverifikasi')
                            ->body('Pekerjaan On Call Anda untuk ' . Carbon::parse($record->overtime_date)->translatedFormat('d F Y') . ' telah diverifikasi SDM')
                            ->success()
                            ->actions([
                                Action::make('read')
                                    ->label('Lihat')
                                    ->button()
                                    ->url(OnCallResource::getUrl('index'))
                                    ->markAsRead()
                            ])
                            ->sendToDatabase($recipient);

                        Notification::make()
                            ->title('On Call diverifikasi')
                            ->success()
                            ->send();
                    }),
                Action::make('cancel')
                    ->label('Batalkan')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn ($record, $livewire) => 
                        is_null($record->is_verified) && 
                        $livewire->tableFilters['role_view']['value'] == 'as_verifier')
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
                            ->title('Pekerjaan On Call Ditolak SDM')
                            ->body('Pekerjaan On Call Anda untuk ' . Carbon::parse($record->overtime_date)->translatedFormat('d F Y') . ' telah ditolak SDM dengan alasan: ' . $data['note'])
                            ->success()
                            ->actions([
                                Action::make('read')
                                    ->label('Lihat')
                                    ->button()
                                    ->url(OvertimeResource::getUrl('index'))
                                    ->markAsRead()
                            ])
                            ->sendToDatabase($recipient);

                        Notification::make()
                            ->title('On Call dibatalkan')
                            ->success()
                            ->send();
                    }),
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (OnCall $record) => $record->command_by == Auth::user()->staff_id && is_null($record->is_verified) && is_null($record->is_known)),
                DeleteAction::make()
                    ->visible(fn (OnCall $record) => $record->command_by == Auth::user()->staff_id && is_null($record->is_verified) && is_null($record->is_known)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageOnCalls::route('/'),
        ];
    }
}
