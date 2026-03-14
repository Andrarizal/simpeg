<?php

namespace App\Filament\Resources\Schedules\Pages;

use App\Filament\Resources\Schedules\ScheduleResource;
use App\Filament\Resources\ShiftExchanges\ShiftExchangeResource;
use App\Models\Chair;
use App\Models\Schedule;
use App\Models\Shift;
use App\Models\ShiftExchange;
use App\Models\Staff;
use App\Models\Unit;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ManageSchedules extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;
    
    protected static string $resource = ScheduleResource::class;

    protected string $view = 'filament.resources.units.pages.manage-unit-schedule';

    public ?Unit $record;
    public Collection $staffList;
    public $month;
    public $year;
    public $schedules = [];
    public $daysInMonth = [];
    
    public function mount(): void
    {
        $user = Auth::user();
        $unitId = $user->staff?->unit_id;

        if ($unitId == 1){
            $this->record = Chair::where('head_id', $user->staff->chair_id)->first()?->unit;
        } else {
            $this->record = Unit::find($unitId);
        }

        $this->month = now()->month;
        $this->year = now()->year;
    }

    public function getTitle(): string
    {
        return 'Jadwal Unit: ' . $this->record->name;
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
                ->visible(function() {
                    $isLeader = Auth::user()->staff->chair_id == Auth::user()->staff->unit?->leader_id;
                    return $isLeader;
                })
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
                                ->native(false)
                                ->displayFormat('H:i'),
                            TimePicker::make('end_time')
                                ->label('Pulang')
                                ->seconds(false)
                                ->native(false)
                                ->displayFormat('H:i'),
                            Hidden::make('id'),
                        ])
                        ->maxItems(fn () => $this->record->work_system == 'Tetap' ? 1 : null)
                        ->minItems(1)
                        ->columns(3)
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                ])
                ->action(function (array $data) {
                    $shiftsInput = collect($data['shift']);

                    $idsToKeep = $shiftsInput->pluck('id')->filter()->toArray();

                    $this->record->shift()
                        ->where('is_off', false)
                        ->whereNotIn('id', $idsToKeep)
                        ->delete();

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
                ->visible(function() {
                    $isLeader = Auth::user()->staff->chair_id == Auth::user()->staff->unit?->leader_id;
                    return $isLeader && $this->record->work_system == 'Tetap';
                })
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
                    
                    $dataToInsert = [];
                    $now = now();

                    foreach ($staffs as $staff) {
                        for ($day = 1; $day <= $totalDays; $day++) {
                            $date = Carbon::create($year, $month, $day);
                            $shiftToAssign = $date->isSunday() ? ($shiftLibur?->id) : $shiftReguler->id;

                            if ($shiftToAssign) {
                                $dataToInsert[] = [
                                    'staff_id' => $staff->id,
                                    'schedule_date' => $date->toDateString(),
                                    'shift_id' => $shiftToAssign,
                                    'created_at' => $now,
                                    'updated_at' => $now,
                                ];
                            }
                        }
                    }

                    foreach (array_chunk($dataToInsert, 500) as $chunk) {
                        Schedule::upsert(
                            $chunk, 
                            ['staff_id', 'schedule_date'], 
                            ['shift_id', 'updated_at']
                        );
                    }

                    Notification::make()
                        ->title("Berhasil generate jadwal untuk {$staffs->count()} pegawai.")
                        ->success()
                        ->send();
                }),
            Action::make('randomizing_schedule')
                ->label('Generate')
                ->icon('heroicon-m-bolt')
                ->color('warning')
                ->visible(function() {
                    $isLeader = Auth::user()->staff->chair_id == Auth::user()->staff->unit?->leader_id;
                    return $isLeader && $this->record->work_system == 'Shift';
                })
                ->modalHeading('Generate Jadwal Otomatis')
                ->modalWidth('sm')
                ->modalDescription('Fitur ini akan mengisi jadwal seluruh pegawai di unit ini secara otomatis (Satu hari Libur untuk setiap pegawai dalam satu pekan dengan total jam kerja merata).')
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
                    $workingShifts = Shift::where('unit_id', $this->record->id)->where('is_off', false)->get();
                    $shiftLibur    = Shift::where('unit_id', $this->record->id)->where('is_off', true)->first();

                    if ($workingShifts->isEmpty() || !$shiftLibur) {
                        Notification::make()->title('Gagal: Belum ada Shift Masuk di unit ini!')->danger()->send();
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
                    
                    $dataToInsert = [];
                    $now = now();

                    foreach ($staffs as $staffIndex => $staff) {
                        // Buat array hari dari 1 sampai akhir bulan (misal: [1, 2, 3 ... 31])
                        $daysArray = range(1, $totalDays);
                        
                        // Pecah array hari tersebut menjadi per minggu (7 hari)
                        $weeks = array_chunk($daysArray, 7);
                        
                        // Pointer untuk rotasi shift (Staf A mulai dari Shift Pagi, Staf B mulai dari Shift Siang, dst)
                        $shiftPointer = $staffIndex % $workingShifts->count();

                        // 3. Looping per Minggu untuk pegawai tersebut
                        foreach ($weeks as $weekDays) {
                            // Pilih 1 hari secara acak dari minggu ini untuk dijadikan hari libur
                            $dayOff = $weekDays[array_rand($weekDays)];

                            // 4. Looping per Hari dalam minggu tersebut
                            foreach ($weekDays as $day) {
                                $date = Carbon::create($year, $month, $day);
                                
                                if ($day === $dayOff) {
                                    // Berikan shift libur
                                    $shiftToAssign = $shiftLibur->id;
                                } else {
                                    // Berikan shift kerja berdasarkan urutan (Round-Robin)
                                    $shiftToAssign = $workingShifts[$shiftPointer]->id;
                                    
                                    // Putar pointer shift ke selanjutnya (Pagi -> Siang -> Malam -> Pagi lagi)
                                    $shiftPointer = ($shiftPointer + 1) % $workingShifts->count();
                                }

                                $dataToInsert[] = [
                                    'staff_id'      => $staff->id,
                                    'schedule_date' => $date->toDateString(),
                                    'shift_id'      => $shiftToAssign,
                                    'created_at'    => $now,
                                    'updated_at'    => $now,
                                ];
                            }
                        }
                    }

                    // 5. Simpan ke Database
                    foreach (array_chunk($dataToInsert, 500) as $chunk) {
                        Schedule::upsert(
                            $chunk, 
                            ['staff_id', 'schedule_date'], 
                            ['shift_id', 'updated_at']
                        );
                    }

                    Notification::make()
                        ->title("Berhasil generate jadwal Shift untuk {$staffs->count()} pegawai.")
                        ->success()
                        ->send();
                }),
            Action::make('exchange')
                ->label('Tukar Jadwal')
                ->color('info')
                ->icon('heroicon-m-arrow-path-rounded-square')
                ->visible(fn () => $this->record->work_system == 'Shift' && Auth::user()->staff->chair_id != Auth::user()->staff->unit->leader_id)
                ->modalHeading('Tukar Jadwal')
                ->modalWidth('xl')
                ->modalDescription('PERHATIAN: Fitur ini akan mengajukan penukaran jadwal Anda pada tanggal yang telah Anda tentukan dengan rekan kerja Anda. Jika hendak menukar libur, maka lakukan pada kedua tanggal yang akan ditukar')
                ->modalSubmitActionLabel('Tukar Jadwal')
                ->modalFooterActionsAlignment('center')
                ->schema([
                    Grid::make(2)
                        ->schema([
                        DatePicker::make('exchange_date')
                            ->label('Tanggal Tukar')
                            ->required()
                            ->minDate(fn () => Carbon::today())
                            ->columnSpan(fn (Get $get) => !$get('staff_schedule_id') ? 2 : 1)
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                $set('replacer_id', null);
                                $set('staff_schedule_id', null);
                                $set('replacer_schedule_id', null);

                                if (!$state) return;

                                $myId = Auth::user()->staff->id;
                                $mySchedule = Schedule::where('staff_id', $myId)
                                    ->where('schedule_date', $state)
                                    ->first();

                                if ($mySchedule) {
                                    $set('staff_schedule_id', $mySchedule->id);
                                } else {
                                    Notification::make()
                                        ->title('Jadwal Tidak Ditemukan')
                                        ->body('Anda tidak memiliki jadwal kerja pada tanggal ini.')
                                        ->warning()
                                        ->send();
                                    $set('exchange_date', null); // Clear tanggal
                                }
                            }),

                        TextEntry::make('staff_schedule_info')
                            ->label('Jadwal Anda Saat Ini')
                            ->hidden(fn(Get $get) => !$get('staff_schedule_id'))
                            ->state(function(Get $get){
                                $scheduleId = $get('staff_schedule_id');
                                if (!$scheduleId) return '-';

                                $schedule = Schedule::find($scheduleId);
                                $shiftName = $schedule->shift->name;
                                $time = Carbon::parse($schedule->shift->start_time)->format('H:i') . ' - ' . Carbon::parse($schedule->shift->end_time)->format('H:i');
                                
                                return new HtmlString("
                                    <span class='font-bold text-primary-600'>{$shiftName}</span> <br> 
                                    <span class='text-sm text-gray-500'>($time)</span>
                                ");
                            }),

                        Select::make('replacer_id')
                            ->label('Rekan Pengganti')
                            ->placeholder('Pilih rekan kerja...')
                            ->searchable()
                            ->options(function () {
                                $user = Auth::user();

                                return Staff::where('unit_id', $user->staff->unit_id)
                                    ->where('id', '!=', $user->staff_id)
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->columnSpan(fn (Get $get) => !$get('replacer_schedule_id') ? 2 : 1)
                            ->visible(fn (Get $get) => $get('staff_schedule_id'))
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                $date = $get('exchange_date');
                                if (!$date || !$state) return;
                                
                                $replacerSchedule = Schedule::where('staff_id', $state)
                                    ->where('schedule_date', $date)
                                    ->first();

                                if ($replacerSchedule) {
                                    $set('replacer_schedule_id', $replacerSchedule->id);
                                } else {
                                    Notification::make()
                                        ->title('Jadwal Pengganti Tidak Ditemukan')
                                        ->body('Rekan yang Anda pilih tidak memiliki jadwal (Libur/Cuti) pada tanggal tersebut.')
                                        ->danger()
                                        ->send();
                                    $set('replacer_id', null);
                                }
                            }),

                        TextEntry::make('replacer_schedule_info')
                            ->label('Jadwal Rekan Pengganti')
                            ->visible(fn (Get $get) => $get('replacer_schedule_id'))
                            ->state(function (Get $get) {
                                $scheduleId = $get('replacer_schedule_id');
                                if (!$scheduleId) return '-';

                                $schedule = Schedule::find($scheduleId);
                                $shiftName = $schedule->shift->name;
                                $time = Carbon::parse($schedule->shift->start_time)->format('H:i') . ' - ' . Carbon::parse($schedule->shift->end_time)->format('H:i');

                                return new HtmlString("
                                    <div class='p-2 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700'>
                                        <span class='font-bold text-warning-600'>{$shiftName}</span> ($time)
                                    </div>
                                ");
                            }),

                        Textarea::make('reason')
                            ->label('Alasan Tukar Jadwal')
                            ->rows(3)
                            ->visible(fn (Get $get) => $get('replacer_schedule_id'))
                            ->columnSpanFull(),

                        Hidden::make('staff_id')
                            ->default(Auth::user()->staff->id),
                        
                        Hidden::make('staff_schedule_id')
                            ->required(),
                            
                        Hidden::make('replacer_schedule_id')
                            ->required(),
                    ])
                ])
                ->action(function (array $data): void {
                    ShiftExchange::create([
                        'exchange_date'        => $data['exchange_date'],
                        'staff_id'             => $data['staff_id'],
                        'staff_schedule_id'    => $data['staff_schedule_id'],
                        'replacer_id'          => $data['replacer_id'],
                        'replacer_schedule_id' => $data['replacer_schedule_id'],
                        'reason'               => $data['reason'],
                        'status'               => 'Menunggu',
                    ]);

                    Notification::make()
                        ->title('Permohonan Terkirim')
                        ->body('Pengajuan tukar jadwal berhasil dibuat dan menunggu persetujuan.')
                        ->success()
                        ->send();

                    $staff = Auth::user()->staff;
                    $isLeader = $staff->unit?->leader_id;
                    $recipient = $isLeader ? $staff->unit->leader->user : $staff->chair->parent->user;

                    if ($recipient) {
                        Notification::make()
                            ->title($staff->name . ' telah mengajukan tukar jadwal')
                            ->body($staff->name . ' mengajukan tukar jadwal untuk tanggal ' . Carbon::parse($data['exchange_date'])->translatedFormat('d F Y'))
                            ->warning()
                            ->actions([
                                Action::make('read')
                                    ->label('Lihat')
                                    ->button()
                                    ->url(ShiftExchangeResource::getUrl('index'))
                                    ->markAsRead()
                            ])
                            ->sendToDatabase($recipient);
                    }
                }),
        ];
    }

    public function table(Table $table): Table
    {
        $month = (int) ($this->tableFilters['month']['value'] ?? now()->month);
        $year  = (int) ($this->tableFilters['year']['value']  ?? now()->year);
        
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $unitId = $this->record->id;

        $shifts = Shift::where('unit_id', $unitId)->get();

        $shiftOptions = $shifts->pluck('code', 'id')->toArray();

        $shiftDurations = $shifts->mapWithKeys(function ($shift) {
            $start = Carbon::parse($shift->start_time);
            $end = Carbon::parse($shift->end_time);
            
            if ($end->lessThan($start)) {
                $end->addDay();
            }
            
            return [$shift->id => $start->floatDiffInHours($end)];
        })->toArray();
        
        $dateColumns = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateObj = Carbon::createFromDate($year, $month, $day);
            $dateString = $dateObj->toDateString();
            $dayName = $dateObj->locale('id')->isoFormat('ddd'); 

            $headerHtml = new HtmlString("
                <div class='flex flex-col items-center justify-center leading-none min-h-[30px]'>
                    <span class='text-xl font-black text-gray-700 dark:text-gray-200'>
                        {$day}
                    </span>
                    <span class='text-[10px] font-medium text-gray-400 uppercase tracking-wide mt-1'>
                        {$dayName}
                    </span>
                </div>
            ");

            $dateColumns[] = ViewColumn::make("col_{$day}")
                ->label($headerHtml)
                ->alignment('center')
                ->state(function (Staff $record) use ($dateString) {
                    return $record->schedule->firstWhere('schedule_date', $dateString)?->shift_id;
                })
                ->disabled(function () use ($year, $month) {
                    $user = Auth::user();
                    $isLeader = $user->staff->chair_id == $user->staff->unit?->leader_id;

                    $isPast = ($year < now()->year) || ($year == now()->year && $month < now()->month);

                    return !$isLeader || $isPast;
                })
                ->view('filament.components.native-select')
                ->viewData([
                    'date' => $dateString,
                    'options' => $shiftOptions,
                ]);
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
                        ->extraHeaderAttributes([
                            'class' => 'sticky left-0 z-10 bg-gray-50 bg-gray-100 dark:bg-gray-800',
                        ])
                        ->extraAttributes([
                            'class' => 'sticky-col-name', 
                        ]),
                ],
                $dateColumns,
                [
                    TextColumn::make('total_hours')
                        ->label('Total Jam')
                        ->alignCenter()
                        ->state(function (Staff $record) use ($shiftDurations) {
                            $total = $record->schedule->sum(function ($schedule) use ($shiftDurations) {
                                return $shiftDurations[$schedule->shift_id] ?? 0;
                            });

                            return $total;
                        })
                        ->formatStateUsing(fn ($state) => number_format((float)$state, 1, '.', '') . ' Jam')
                        ->color('primary')
                        ->weight('bold')
                        ->extraHeaderAttributes([
                            'class' => 'sticky right-0 z-10 bg-gray-50 bg-gray-100 dark:bg-gray-800', 
                        ])
                        ->extraAttributes([
                            'class' => 'sticky-col-total',
                        ])
                ]
            ))
            ->paginated(false)
            ->filters([
                SelectFilter::make('month')
                    ->label('Bulan')
                    ->options(collect(range(1, 12))->mapWithKeys(fn($m) => 
                        [$m => Carbon::create(2024, $m, 1)->locale('id')->monthName]
                    ))
                    ->indicateUsing(function (array $data) {
                        return [
                            Indicator::make('Bulan: ' . Carbon::create(0, $data['value'])->locale('id')->monthName)
                                ->removable(false),
                        ];
                    })
                    ->default(now()->month)
                    ->selectablePlaceholder(false)
                    ->query(fn($query) => $query),
                SelectFilter::make('year')
                    ->label('Tahun')
                    ->options(collect(range(now()->year - 1, now()->year + 5))->mapWithKeys(fn($y) => 
                        [$y => $y]
                    ))
                    ->indicateUsing(function (array $data) {
                        return [
                            Indicator::make('Tahun: ' . $data['value'])
                                ->removable(false),
                        ];
                    })
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
            );
    }

    public function getSubheading(): string|Htmlable|null
    {
        if (! $this->record) {
            return null;
        }

        $shiftItems = $this->record->shift
        ->sortBy(function ($row) {
            $isLibur = $row->code == 'L' || !$row->start_time;
            return ($isLibur ? 'Z' : 'A') . '-' . ($row->start_time ?? '00:00');
        })
        ->map(function ($row) {
            $start = Carbon::parse($row->start_time ?? '00:00:00')->format('H:i');
            $end   = Carbon::parse($row->end_time ?? '00:00:00')->format('H:i');

            return "
                <div class='flex items-center gap-1 whitespace-nowrap bg-gray-100 dark:bg-white/5 px-2 py-1 rounded-md border border-gray-200 dark:border-white/10'>
                    <span class='font-bold text-primary-600 dark:text-primary-400'>{$row->code} ($row->name):</span>
                    <span class='text-gray-700 dark:text-gray-300'>{$start}-{$end}</span>
                </div>
            ";
        })->implode('');

        return new HtmlString("
            <div class='flex flex-wrap items-center gap-2 mt-2 text-xs'>
                <div class='flex items-center justify-center w-6 h-6 bg-gray-100 dark:bg-gray-800 rounded-full shrink-0'>
                    <svg class='w-4 h-4 text-gray-500' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor'>
                        <path fill-rule='evenodd' d='M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z' clip-rule='evenodd' />
                    </svg>
                </div>
                
                {$shiftItems}
            </div>
        ");
    }

    public function updateShift($staffId, $date, $value)
    {
        $isStaffInUnit = Staff::where('id', $staffId)
            ->where('unit_id', $this->record->id)
            ->exists();

        if (!$isStaffInUnit) return; 

        if (empty($value) || $value == '-') {
            Schedule::where('staff_id', $staffId)
                ->where('schedule_date', $date)
                ->delete();
            return;
        }

        Schedule::updateOrCreate(
            ['staff_id' => $staffId, 'schedule_date' => $date],
            ['shift_id' => $value]
        );
        
        Notification::make()->title('Saved')->success()->send();
    }
}
