<?php

namespace App\Filament\Resources\Performances\Pages;

use App\Filament\Resources\Performances\PerformanceResource;
use App\Models\Chair;
use App\Models\PerformanceAppraisal;
use App\Models\PerformancePeriod;
use App\Models\Staff;
use App\Models\StaffPerformance;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ManagePerformances extends ManageRecords
{
    protected static string $resource = PerformanceResource::class;

    public function mount(): void
    {
        if (request()->has('activeTab')) {
            $this->activeTab = request()->query('activeTab');
        }

        parent::mount();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['staff_id'] = Auth::user()->staff_id;
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Capaian')
                ->modalHeading('Tambah Capaian untuk Periode Ini')
                ->modalWidth('xl')
                ->createAnother(false)
                ->visible(fn() => (!$this->activeTab || $this->activeTab == 'sendiri') && (function(){
                    $period = PerformancePeriod::where('status', 1)->latest()->value('id');
                    $performance = StaffPerformance::where('period_id', $period)->where('staff_id', Auth::user()->staff_id)->first();

                    if ($performance) return false;
                    return true;
                })())
                ->schema([
                    Grid::make()
                        ->schema([
                            Select::make('staff_id')
                                ->label('Nama Pegawai')
                                ->options(Staff::all()->pluck('name', 'id'))
                                ->default(Auth::user()->staff_id) 
                                ->disabled() 
                                ->dehydrated(),
                            Select::make('period_id')
                                ->label('Periode')
                                ->options(function(){
                                    return PerformancePeriod::query()
                                    ->orderBy('start_date', 'desc')
                                    ->get()
                                    ->mapWithKeys(function ($period) {
                                        $start = Carbon::parse($period->start_date)->translatedFormat('F');
                                        $end = Carbon::parse($period->end_date)->translatedFormat('F Y');
                                        return [$period->id => "$start - $end"];
                                    });
                                })
                                ->default(function(){
                                    // return PerformancePeriod::query()
                                    //     ->whereDate('start_date', '<=', now())
                                    //     ->whereDate('end_date', '>=', now())
                                    //     ->value('id');

                                    return PerformancePeriod::where('status', 1)->latest()->value('id');
                                })
                                ->disabled() 
                                ->dehydrated()
                                ->selectablePlaceholder(false),
                        ]),
                    TextInput::make('title')
                        ->label('Judul Capaian')
                        ->maxLength(255)
                        ->required(),
                    Textarea::make('description')
                        ->label('Deskripsi Capaian')
                        ->rows(3)
                        ->required()
                ]),
            Action::make('periods')
                ->label('Kelola Periode')
                ->modalHeading('Manajemen Periode Penilaian')
                ->modalContent(view('filament.pages.partials.period-manager-modal')) 
                ->modalSubmitAction(false) 
                ->modalCancelAction(false)
                ->modalWidth('xl')
                ->icon('heroicon-o-swatch')
                ->color('warning')
                ->visible(fn() => Auth::user()->role_id == 1)
                ->slideOver(),
        ];
    }

    public function getTabs(): array
    {
        $user = Auth::user();
        $user->staff_id = $user->staff_id ?? 1;

        $isAppraiser = ($user->staff->chair->level == 4 && $user->staff->unit->leader_id == $user->staff->chair_id) 
           || ($user->staff->chair->level == 4 && $user->role_id == 1) 
           || $user->staff->chair->level < 4;

        $tabs = [];
        
        if ($isAppraiser){
            $label = $user->role_id == 1 ? 'Tinjauan' : 'Penilaian';
            
            $tabs['sendiri'] = Tab::make('Kinerja Saya')
                ->icon('heroicon-o-document-text');
            $tabs['penilaian'] = Tab::make("Perlu $label")
                ->icon('heroicon-o-clipboard-document-check');
        }

        return $tabs;
    }

    public function updatedActiveTab(): void
    {
        parent::updatedActiveTab(); 
        $this->redirect(static::getResource()::getUrl('index', ['activeTab' => $this->activeTab]));
    }

    public function table(Table $table): Table
    {
        $activeTab = $this->activeTab ?? 'sendiri';

        if ($activeTab == 'penilaian') {
            return $table
                ->query(function(): Builder {
                    $staff = Auth::user()->staff;
                    $query = StaffPerformance::query();
                    $query->where('staff_id', '!=', $staff->id)
                        ->with([
                            'period', 
                            'staff.chair', 
                            'staff.unit', 
                            'appraisal.appraiser.chair' 
                        ]);

                    if (Auth::user()->role_id == 1){
                        $query->latest();
                    } else {
                        if ($staff->chair->level == 4){
                            $query->whereHas('staff.chair', function ($q) use ($staff) {
                                $q->where('head_id', $staff->chair->head_id)->where('level', $staff->chair->level);
                            });
                        } else if ($staff->chair->level != 1) {
                            $heads = StaffPerformance::with(['staff.chair'])
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
                    return $query->latest();
                })
                ->columns([
                    TextColumn::make('staff.name')
                        ->label('Nama Pegawai')
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('title')
                        ->label('Capaian Kinerja')
                        ->searchable()
                        ->wrap(),
                    TextColumn::make('description')
                        ->label('Isi / Deskripsi')
                        ->limit(250)
                        ->formatStateUsing(fn ($state) => $state . '...')
                        ->wrap()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('appraisal.score')
                        ->label('Nilai')
                        ->state(function (StaffPerformance $record) {
                            $avg = $record->appraisal()->avg('score'); 
                            return $avg ? number_format($avg, 1) : '-';
                        })
                        ->badge()
                        ->color(fn ($state) => match (true) {
                            $state >= 85 => 'info', 
                            $state >= 70 => 'success', 
                            $state >= 50 => 'warning', 
                            $state > 0   => 'danger',  
                            default      => 'gray',
                        })
                        ->tooltip(function (StaffPerformance $record) {
                            if ($record->appraisal){
                                return match (Auth::user()->staff->chair->level) {
                                    4 => $record->appraisal->appraiser->chair->level == 4 && Auth::user()->role_id != 1 ? 'Input Nilai' : null,
                                    3 => in_array($record->appraisal->appraiser->chair->level, [3,4]) ? 'Input Nilai' : null,
                                    2 => in_array($record->appraisal->appraiser->chair->level, [2,3]) ? 'Input Nilai' : null,
                                    1 => in_array($record->appraisal->appraiser->chair->level, [1,2]) ? 'Input Nilai' : null,
                                };
                            } else {
                                // Atur penginput nilai pertama kali
                                if (Auth::user()->staff->chair->level == 4){
                                    if (!Auth::user()->staff->unit->leader_id) return null;
                                } else {
                                    if (Auth::user()->staff->chair->level == 3){
                                        if ($record->staff->unit->leader_id) return null;
                                    } else {
                                        return null;
                                    }
                                }
                            }
                            return 'Input Nilai';
                        })
                        ->action(
                            Action::make('rate')
                                ->label('Input Penilaian')
                                ->modalWidth('md')
                                ->modalHeading(fn ($record) => "Nilai Kinerja: {$record->staff->name}")
                                ->schema([
                                    TextInput::make('score')
                                        ->label('Nilai (0-100)')
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->required()
                                        ->autofocus(),
                                
                                    Textarea::make('notes')
                                        ->label('Catatan / Masukan')
                                        ->rows(3)
                                        ->placeholder('Berikan alasan penilaian...'),
                                ])
                                ->fillForm(function (StaffPerformance $record) {
                                    $existing = $record->appraisal()
                                        ->first();

                                    return [
                                        'score' => $existing?->score,
                                        'notes' => $existing?->notes,
                                    ];
                                })
                                ->action(function (array $data, StaffPerformance $record) {
                                    $staff = Auth::user()->staff_id;

                                    PerformanceAppraisal::updateOrCreate(
                                        [
                                            'target_id' => $record->id,
                                        ],
                                        [
                                            'appraiser_id' => $staff,
                                            'score' => $data['score'],
                                            'notes' => $data['notes'],
                                        ]
                                    );

                                    Notification::make()->title('Penilaian Disimpan')->success()->send();
                                })
                                ->disabled(function (StaffPerformance $record) {
                                    if ($record->appraisal){
                                        return match (Auth::user()->staff->chair->level) {
                                            4 => $record->appraisal->appraiser->chair->level == 4 && Auth::user()->role_id != 1 ? false : true,
                                            3 => in_array($record->appraisal->appraiser->chair->level, [3,4]) ? false : true,
                                            2 => in_array($record->appraisal->appraiser->chair->level, [2,3]) ? false : true,
                                            1 => in_array($record->appraisal->appraiser->chair->level, [1,2]) ? false : true,
                                        };
                                    } else {
                                        // Atur penginput nilai pertama kali
                                        if (Auth::user()->staff->chair->level == 4){
                                            if (!Auth::user()->staff->unit->leader_id) return true;
                                        } else {
                                            if (Auth::user()->staff->chair->level == 3){
                                                if ($record->staff->unit->leader_id) return true;
                                            } else {
                                                return true;
                                            }
                                        }
                                    }
                                    return false;
                                })
                        ),
                    TextColumn::make('appraiser') // Beri nama unik sembarang
                        ->label('Telah Dinilai Oleh')
                        ->state(function (StaffPerformance $record) {
                            if (!$record->appraisal) {
                                return '-';
                            } 
                            
                            return match ($record->appraisal->appraiser->chair->level) {
                                4 => 'Kepala Unit',
                                3 => 'Koordinator',
                                2 => 'Kepala Seksi',
                                1 => 'Direktur',
                                default => false,
                            };
                        })
                ])
                ->filters([
                    SelectFilter::make('period_id')
                        ->label('Periode Penilaian')
                        ->options(function () {
                            return PerformancePeriod::orderBy('start_date', 'desc')
                                ->get()
                                ->mapWithKeys(function ($period) {
                                    $start = Carbon::parse($period->start_date)->translatedFormat('M');
                                    $end = Carbon::parse($period->end_date)->translatedFormat('M Y');
                                    return [$period->id => "$start - $end"];
                                });
                        })
                        ->default(function () {
                            return PerformancePeriod::where('status', true)->latest()->first()?->id;
                        })
                        ->searchable()
                        ->selectablePlaceholder(false)
                        ->native(false),
                ])
                ->hiddenFilterIndicators()
                ->contentFooter(view('filament.tables.avgscore-pagination', [
                    'score' => $this->averageScore, // <--- Ini otomatis dinamis
                ]))
                ->recordActions([
                    Action::make('approve')
                        ->label('Setujui Nilai')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->visible(fn ($record) => $record->appraisal->appraiser->chair->level > Auth::user()->staff->chair->level)
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            $user = Auth::user();
                            $user->staff_id = $user->staff_id ?? 1;

                            $record->appraisal->update([
                                'appraiser_id' => $user->staff_id
                            ]);

                            Notification::make()
                                ->title('Nilai berhasil disetujui')
                                ->success()
                                ->send();
                        }),
                    ViewAction::make(),
                ]);
        } else {
            return $table
                ->query(function(): Builder {
                    $staff = Auth::user()->staff;
                    $query = StaffPerformance::query();
                    $query->where('staff_id', $staff->id)
                        ->with(['period', 'appraisal.appraiser.chair']);

                    return $query->latest();
                })
                ->columns([
                    TextColumn::make('period_id') 
                    ->label('Periode Bulan')
                    ->state(function (StaffPerformance $record) {
                        $start = Carbon::parse($record->period->start_date);
                        $end = Carbon::parse($record->period->end_date);

                        return $start->translatedFormat('F') . ' - ' . $end->translatedFormat('F') . $start->translatedFormat(' Y');
                    }),
                    TextColumn::make('title')
                        ->label('Capaian Kinerja')
                        ->searchable()
                        ->wrap(),
                    TextColumn::make('description')
                        ->label('Isi / Deskripsi')
                        ->limit(250)
                        ->formatStateUsing(fn ($state) => $state . '...')
                        ->wrap()
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('appraisal.score')
                        ->label('Nilai')
                        ->state(function (StaffPerformance $record) {
                            $avg = $record->appraisal()->avg('score'); 
                            return $avg ? number_format($avg, 1) : '-';
                        })
                        ->badge()
                        ->color(fn ($state) => match (true) {
                            $state >= 85 => 'info', 
                            $state >= 70 => 'success', 
                            $state >= 50 => 'warning', 
                            $state > 0   => 'danger',  
                            default      => 'gray',
                        }),
                    TextColumn::make('appraiser')
                        ->label('Telah Dinilai Oleh')
                        ->state(function (StaffPerformance $record) {
                            if (!$record->appraisal) {
                                return '-';
                            } 
                            
                            return match ($record->appraisal->appraiser->chair->level) {
                                4 => 'Kepala Unit',
                                3 => 'Koordinator',
                                2 => 'Kepala Seksi',
                                1 => 'Direktur',
                                default => false,
                            };
                        })
                ])
                ->recordActions([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                ])
                ->toolbarActions([
                    BulkActionGroup::make([
                        DeleteBulkAction::make(),
                    ]),
                ]);
        }
    }

    public function getAverageScoreProperty()
    {
        $selectedPeriodId = $this->tableFilters['period_id']['value'] ?? null;

        if ($selectedPeriodId) {
            $period = PerformancePeriod::find($selectedPeriodId);
        } else {
            $period = PerformancePeriod::where('status', true)->latest()->first();
        }

        return $period->score ?? 0;
    }
}
