<?php

namespace App\Filament\Resources\Trainings;

use App\Filament\Resources\Trainings\Pages\ManageTrainings;
use App\Models\Staff;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Actions;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use UnitEnum;

class TrainingResource extends Resource
{
    protected static ?string $model = Staff::class;

    protected static ?string $modelLabel = 'Pelatihan Pegawai';       
    protected static ?string $pluralModelLabel = 'Pelatihan Pegawai'; 
    protected static ?string $navigationLabel = 'Pelatihan Pegawai';
    protected static ?int $navigationSort = 5;
    protected static UnitEnum|string|null $navigationGroup = 'Keperluan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Swatch;

    protected static ?string $recordTitleAttribute = 'Pelatihan Pegawai';

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->extraAttributes([
                            'class' => '[&_.fi-section-content]:p-0',
                        ])
                    ->schema([
                        Group::make()
                            ->extraAttributes([
                                'class' => 'overflow-x-auto',
                            ])
                            ->schema([
                                Group::make()
                                    ->extraAttributes(['class' => 'min-w-[800px]'])
                                    ->gap(false)
                                    ->schema([
                                        Grid::make([
                                            'default' => str_contains(Auth::user()->staff->chair->name, 'Diklat') ? 12 : 10
                                        ])
                                            ->extraAttributes(['class' => 'border-b border-gray-200 dark:border-gray-700 pb-2 font-bold text-sm bg-gray-50 dark:bg-gray-800/50 p-2 rounded-t-lg',])
                                            ->schema([
                                                TextEntry::make('h_name')
                                                    ->state('Nama Pelatihan')
                                                    ->weight(FontWeight::Bold)
                                                    ->hiddenLabel()
                                                    ->columnSpan([
                                                        'default' => 3
                                                    ]),

                                                TextEntry::make('h_dur')
                                                    ->state('Durasi')
                                                    ->alignCenter()
                                                    ->weight(FontWeight::Bold)
                                                    ->hiddenLabel()
                                                    ->columnSpan([
                                                        'default' => 2
                                                    ]),

                                                TextEntry::make('h_date')
                                                    ->state('Tanggal')
                                                    ->alignCenter()
                                                    ->weight(FontWeight::Bold)
                                                    ->hiddenLabel()
                                                    ->columnSpan([
                                                        'default' => 3
                                                    ]),

                                                TextEntry::make('h_stat')
                                                    ->state('Status')
                                                    ->alignCenter()
                                                    ->weight(FontWeight::Bold)
                                                    ->hiddenLabel()
                                                    ->columnSpan([
                                                        'default' => 1
                                                    ]),

                                                TextEntry::make('h_act')
                                                    ->state('Aksi')
                                                    ->hiddenLabel()
                                                    ->weight(FontWeight::Bold)
                                                    ->alignCenter()
                                                    ->visible(fn() => str_contains(Auth::user()->staff->chair->name, 'Diklat'))
                                                    ->columnSpan([
                                                        'default' => 3
                                                    ]),
                                            ]),

                                        RepeatableEntry::make('training')
                                            ->hiddenLabel()
                                            ->contained(false)
                                            ->getStateUsing(function (Model $record, Component $livewire) {
                                                $filters = $livewire->tableFilters ?? [];
                                                $selectedYear = $filters['filter_year']['value'] ?? now()->year;
                                                return $record->training()
                                                    ->whereYear('training_date', $selectedYear)
                                                    ->get(); 
                                            })
                                            ->extraAttributes(['class' => 'gap-0'])
                                            ->schema([
                                                Grid::make([
                                                    'default' => str_contains(Auth::user()->staff->chair->name, 'Diklat') ? 12 : 10
                                                ])
                                                    ->extraAttributes(['class' => 'border-b border-gray-100 dark:border-gray-800 py-4 px-2 ' . '[&_.fi-grid]:items-center'])
                                                    ->schema([
                                                        TextEntry::make('name')
                                                            ->hiddenLabel()
                                                            ->weight(FontWeight::Medium)
                                                            ->columnSpan([
                                                                'default' => 3
                                                            ]),
                                                        TextEntry::make('duration')
                                                            ->hiddenLabel()
                                                            ->alignCenter()
                                                            ->columnSpan([
                                                                'default' => 2
                                                                ])
                                                            ->formatStateUsing(fn ($state) => $state . ' jam'),
                                                        TextEntry::make('training_date')
                                                            ->hiddenLabel()
                                                            ->alignCenter()
                                                            ->columnSpan([
                                                                'default' => 3
                                                                ])
                                                            ->date('d F Y'),
                                                        IconEntry::make('is_verified')
                                                            ->hiddenLabel()
                                                            ->default('null')
                                                            ->alignCenter()
                                                            ->columnSpan([
                                                                'default' => 1
                                                                ])
                                                            ->color(fn (string $state): string => match ($state) {
                                                                '1' => 'success',
                                                                '0' => 'danger',
                                                                'null' => 'warning',
                                                            })
                                                            ->icon(fn (string $state): Heroicon => match ($state) {
                                                                '1' => Heroicon::OutlinedCheckCircle,
                                                                '0' => Heroicon::OutlinedXCircle,
                                                                'null' => Heroicon::OutlinedClock,
                                                            }),
                                                        Actions::make([
                                                            Action::make('verified')
                                                                ->label('Verifikasi')
                                                                ->icon('heroicon-o-check')
                                                                ->color('info')
                                                                ->badge()
                                                                ->requiresConfirmation()
                                                                ->visible(fn ($record) => $record->is_verified === null)
                                                                ->action(function ($record) {
                                                                    $record->update([
                                                                        'is_verified' => 1,
                                                                        'verified_at' => now(),
                                                                        'verified_by' => Auth::user()->staff_id,
                                                                    ]);

                                                                    Notification::make()->title('Verifikasi Berhasil')->success()->send();
                                                                }),
                                                            Action::make('reject')
                                                                ->label('Tolak')
                                                                ->icon('heroicon-o-no-symbol')
                                                                ->color('danger')
                                                                ->badge()
                                                                ->visible(fn ($record) => $record->is_verified === null)
                                                                ->schema([
                                                                    Textarea::make('note')
                                                                        ->label('Alasan Penolakan')
                                                                        ->required(),
                                                                ])
                                                                ->action(function ($record, array $data) {
                                                                    $record->update([
                                                                        'is_verified' => 0,
                                                                        'notes' => $data['note'],
                                                                    ]);

                                                                    Notification::make()->title('Pelatihan Ditolak')->danger()->send();
                                                                }),
                                                            ])
                                                            ->hiddenLabel()
                                                            ->visible(fn() => str_contains(Auth::user()->staff->chair->name, 'Diklat'))
                                                            ->alignCenter()
                                                            ->columnSpan([
                                                                'default' => 3
                                                            ]),
                                                    ]),
                                            ])
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->filters([
                SelectFilter::make('filter_year')
                    ->label('Tahun')
                    ->options([
                        2024 => '2024',
                        2025 => '2025',
                        2026 => '2026',
                    ])
                    ->indicateUsing(function (array $data) {
                        return [
                            Indicator::make('Tahun: ' . $data['value'])
                                ->removable(false),
                        ];
                    })
                    ->default(now()->year)
                    ->selectablePlaceholder(false)
                    ->query(fn ($query) => $query),
            ])
            ->modifyQueryUsing(function (Builder $query, Component $livewire) {
                $filterState = $livewire->tableFilters['filter_year']['value'] ?? null;
                $selectedYear = $filterState ?? now()->year;

                return $query->withSum([
                    'training as pending_trainings_count' => function (Builder $q) use ($selectedYear) {
                        $q->whereNull('is_verified')
                        ->whereYear('training_date', $selectedYear);
                    },
                    'training as verified_trainings_count' => function (Builder $q) use ($selectedYear) {
                        $q->where('is_verified', 1)
                        ->whereYear('training_date', $selectedYear);
                    }
                ], 'duration');
            })
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Pegawai')
                    ->description(fn ($record) => $record->nip) 
                    ->searchable()
                    ->sortable(),
                TextColumn::make('unit.name')
                    ->label('Unit Kerja')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('pending_trainings_count')
                    ->label('Belum diverifikasi')
                    ->badge()
                    ->default(0)
                    ->formatStateUsing(fn ($state) => ($state ?? 0) . ' jam')
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'gray')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('verified_trainings_count')
                    ->label('Terverifikasi')
                    ->badge()
                    ->default(0)
                    ->formatStateUsing(fn ($state) => ($state ?? 0) . ' jam')
                    ->color(fn ($state) => $state < setting('training_hours_per_year') ? 'warning' : 'success')
                    ->alignCenter()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTrainings::route('/'),
        ];
    }
}
