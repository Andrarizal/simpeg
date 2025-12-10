<?php

namespace App\Filament\Resources\Units\Pages;

use App\Filament\Resources\Units\UnitResource;
use App\Models\Schedule;
use App\Models\Shift;
use App\Models\Staff;
use App\Models\Unit;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ManageUnitSchedules extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static string $resource = UnitResource::class;

    protected string $view = 'filament.resources.units.pages.manage-unit-schedule';

    protected static ?string $title = 'Jadwal';

    public Unit $record;
    public Collection $staffList;
    public $month;
    public $year;
    public $schedules = [];
    public $shiftOptions = [];
    public $daysInMonth = [];
    
    public function mount(Unit $record): void
    {
        $this->record = $record;
        $this->month = now()->month();
        $this->year = now()->year();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('manage_shifts')
                ->label(fn() => $this->record->work_system == 'Shift' ? 'Kelola Shift' : 'Kelola Jam Kerja')
                ->icon('heroicon-m-cog-6-tooth')
                ->color('gray')
                ->slideOver()
                ->modalWidth('lg')
                ->fillForm(function () {
                    $shifts = $this->record->shift()
                        ->where('is_off', false)
                        ->get()
                        ->map(function ($shift) {
                            return [
                                'id' => $shift->id,
                                'name' => $shift->name,
                                'code' => $shift->code,
                                'start_time' => $shift->start_time ? Carbon::parse($shift->start_time)->format('H:i') : null,
                                'end_time'   => $shift->end_time   ? Carbon::parse($shift->end_time)->format('H:i')   : null,
                            ];
                        })
                        ->toArray();

                    return [
                        'shift' => $shifts
                    ];
                })
                ->schema([
                    Repeater::make('shift')
                        ->label(fn() => $this->record->work_system == 'Shift' ? 'Shift' : 'Jam Kerja')
                        ->hiddenLabel()
                        ->schema([
                            TextInput::make('name')
                                ->label('Nama')
                                ->required()
                                ->columnSpan(3),
                            TextInput::make('code')
                                ->label('Kode')
                                ->maxLength(3),
                            TimePicker::make('start_time')
                                ->label('Masuk')
                                ->seconds(false)
                                ->native(),
                            TimePicker::make('end_time')
                                ->label('Pulang')
                                ->seconds(false)
                                ->native(),
                            Hidden::make('id'),
                        ])
                        ->maxItems(fn () => $this->record->work_system === 'Tetap' ? 1 : null)
                        ->minItems(1)
                        ->columns(3)
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                ])
                ->action(function (array $data) {
                    $shiftsInput = collect($data['shift']);

                    foreach ($shiftsInput as $shiftData) {
                        $this->record->shift()->updateOrCreate(
                            ['id' => $shiftData['id'] ?? null],
                            [
                                'name' => $shiftData['name'],
                                'code' => $shiftData['code'],
                                'start_time' => $shiftData['start_time'],
                                'end_time' => $shiftData['end_time'],
                                'is_off' => false,
                            ]
                        );
                    }

                    $hasOffShift = $this->record->shift()->where('is_off', true)->exists();
                    if (!$hasOffShift) {
                        $this->record->shift()->create([
                            'name' => 'Libur', 
                            'code' => 'L',
                            'is_off' => true,
                        ]);
                        Notification::make()->title('Shift Libur otomatis ditambahkan.')->success()->send();
                    }

                    Notification::make()->title('Master Shift Diperbarui')->success()->send();
                }),
            Action::make('generate_schedule')
                ->label('Generate')
                ->icon('heroicon-m-bolt')
                ->color('warning')
                ->visible(fn () => $this->record->work_system === 'Tetap')
                ->modalHeading('Generate Jadwal Otomatis')
                ->modalWidth('sm')
                ->modalDescription('Fitur ini akan mengisi jadwal seluruh pegawai di unit ini secara otomatis (Senin-Sabtu Masuk, Minggu Libur).')
                ->modalSubmitActionLabel('Generate Jadwal')
                ->modalFooterActionsAlignment('center')
                ->schema([
                    Select::make('month')
                        ->label('Bulan')
                        ->options([
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ])
                        ->default(now()->month)
                        ->required(),
                    
                    TextInput::make('year')
                        ->label('Tahun')
                        ->numeric()
                        ->default(now()->year)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $shiftReguler = Shift::where('unit_id', $this->record->id)->where('is_off', false)->first();
                    $shiftLibur   = Shift::where('unit_id', $this->record->id)->where('is_off', true)->first();

                    if (!$shiftReguler) {
                        Notification::make()->title('Gagal: Shift Masuk belum dibuat di Master Shift unit ini!')->danger()->send();
                        return;
                    }

                    $staffs = Staff::where('unit_id', $this->record->id)->get();

                    if ($staffs->isEmpty()) {
                        Notification::make()->title('Unit ini belum punya pegawai.')->warning()->send();
                        return;
                    }

                    $month = $data['month'];
                    $year  = $data['year'];
                    $totalDays = Carbon::create($year, $month)->daysInMonth;
                    
                    $count = 0;

                    foreach ($staffs as $staff) {
                        for ($day = 1; $day <= $totalDays; $day++) {
                            $date = Carbon::create($year, $month, $day);
                            
                            $shiftToAssign = $date->isSunday() ? ($shiftLibur?->id) : $shiftReguler->id;

                            if ($shiftToAssign) {
                                Schedule::updateOrCreate(
                                    [
                                        'staff_id' => $staff->id,
                                        'schedule_date' => $date->toDateString(),
                                    ],
                                    [
                                        'shift_id' => $shiftToAssign
                                    ]
                                );
                                $count++;
                            }
                        }
                    }

                    Notification::make()
                        ->title("Berhasil generate jadwal untuk {$staffs->count()} pegawai.")
                        ->success()
                        ->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        $month = (int) ($this->tableFilters['month']['value'] ?? now()->month);
        $year  = (int) ($this->tableFilters['year']['value']  ?? now()->year);
        
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        
        $shiftOptions = Shift::where('unit_id', $this->record->id)
            ->pluck('code', 'id')
            ->toArray();

        $dateColumns = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateObj = Carbon::createFromDate($year, $month, $day);
            $dateString = $dateObj->toDateString();
            $headerLabel = $day . ' (' . $dateObj->locale('id')->isoFormat('ddd') . ')'; 

            $dateColumns[] = SelectColumn::make("schedule_{$day}")
                ->label($headerLabel)
                ->options($shiftOptions)
                ->alignment('center')
                ->disabled(fn() => $year <= now()->year && $month < now()->month)
                ->extraAttributes([
                        'style' => 'flex: none !important; width: 90px !important; min-width: 90px !important;',
                        'class' => 'no-arrow'
                    ])
                ->state(function (Staff $record) use ($dateString) {
                    return $record->schedule
                        ->firstWhere('schedule_date', $dateString)
                        ?->shift_id;
                })
                ->updateStateUsing(function (Staff $record, $state) use ($dateString) {
                    if ($state) {
                        Schedule::updateOrCreate(
                            ['staff_id' => $record->id, 'schedule_date' => $dateString],
                            ['shift_id' => $state]
                        );
                    } else {
                        Schedule::where('staff_id', $record->id)
                            ->where('schedule_date', $dateString)
                            ->delete();
                    }
                })
                ->placeholder('-')
                ->selectablePlaceholder(true);
        }

        return $table
            ->query(
                Staff::query()
                    ->where('unit_id', $this->record->id)
                    ->with(['schedule' => function ($q) use ($month, $year) {
                        $q->whereMonth('schedule_date', $month)
                          ->whereYear('schedule_date', $year);
                    }])
            )
            ->columns(array_merge(
                [
                    TextColumn::make('name')
                        ->label('Pegawai')
                        ->searchable()
                        ->sortable()
                        ->description(fn (Staff $record) => $record->chair->name ?? '-')
                ],
                $dateColumns 
            ))
            ->paginated(false)
            ->filters([
                SelectFilter::make('month')
                    ->label('Bulan')
                    ->options(collect(range(1, 12))->mapWithKeys(fn($m) => 
                        [$m => Carbon::create(2024, $m, 1)->locale('id')->monthName]
                    ))
                    ->default(now()->month)
                    ->selectablePlaceholder(false)
                    ->query(fn($query) => $query),

                SelectFilter::make('year')
                    ->label('Tahun')
                    ->options(collect(range(now()->year - 1, now()->year + 5))->mapWithKeys(fn($y) => 
                        [$y => $y]
                    ))
                    ->default(now()->year)
                    ->selectablePlaceholder(false)
                    ->query(fn($query) => $query),
            ])
            ->filtersApplyAction(
                fn (Action $action) => $action
                    ->extraAttributes([
                        'x-data' => '{ isRobot: false, originalText: \'\' }',
                        '@click' => '
                            if (!isRobot) {
                                isRobot = true;
                                let labelSpan = $el.querySelector(\'span\');
                                
                                if (labelSpan) {
                                    originalText = labelSpan.innerText;
                                    labelSpan.innerText = \'Memuat...\';
                                    $el.style.opacity = \'0.7\'; 
                                }

                                setTimeout(() => {
                                    $el.click(); 
                                    setTimeout(() => { 
                                        isRobot = false; 
                                        if (labelSpan) {
                                            labelSpan.innerText = originalText;
                                            $el.style.opacity = \'1\';
                                        }
                                    }, 200);
                                }, 500);
                            }
                        ',
                    ])
            )
            ->filtersFormColumns(2)
            ->hiddenFilterIndicators();
    }
}
