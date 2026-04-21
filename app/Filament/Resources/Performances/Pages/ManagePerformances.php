<?php

namespace App\Filament\Resources\Performances\Pages;

use App\Filament\Resources\Performances\PerformanceResource;
use App\Models\Chair;
use App\Models\PerformanceAppraisal;
use App\Models\PerformancePeriod;
use App\Models\StaffPerformance;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ManagePerformances extends ManageRecords
{
    protected static string $resource = PerformanceResource::class;

    public function mount(): void
    {
        if (request()->has('activeTab')) {
            $this->activeTab = request()->query('activeTab');
        }

        $requestedTab = request()->query('tab') ?? request()->query('activeTab');

        if ($requestedTab === 'penilaian') {
            $user = Auth::user();
            $isBoss = $user->staff->chair->level != 4 || $user->staff->unit->leader_id == $user->staff->chair_id || $user->role_id == 1;

            if (! $isBoss) {
                $this->redirect($this->getResource()::getUrl('index'));
                return;
            }
        }

        parent::mount();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['staff_id'] = Auth::user()->staff_id;
        return $data;
    }

    public function getHeading(): string | Htmlable
    {
        return new HtmlString(<<<HTML
            <div class="flex items-center gap-x-2">
                <span>Penilaian Kinerja</span>
                
                <button 
                    type="button" 
                    wire:click="mountAction('infoAction')" 
                    class="text-primary-500 hover:text-primary-600 transition focus:outline-none" 
                    title="Lihat Panduan Penilaian Kinerja"
                >
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                </button>
            </div>
        HTML);
    }

    public function infoAction(): Action
{
    return Action::make('info')
        ->modalHeading('Panduan Penilaian Kinerja & Capaian')
        ->modalSubmitAction(false)
        ->modalCancelActionLabel('Tutup')
        ->modalWidth('3xl')
        ->modalContent(fn () => new HtmlString('
            <div class="text-sm text-gray-700 dark:text-gray-300 space-y-5">
                <p>Fitur ini digunakan untuk merekam capaian kerja pegawai dan proses penilaiannya secara berjenjang.</p>
                
                <div class="rounded-lg border border-slate-200 bg-slate-50/50 p-4 dark:border-slate-700/50 dark:bg-slate-800/20">
                    <h4 class="font-bold text-slate-700 dark:text-slate-300 mb-2 flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-200 text-xs">1</span>
                        Pembukaan Periode (Khusus SDM)
                    </h4>
                    <ul class="list-disc pl-5 mt-1 space-y-1">
                        <li>Penilaian hanya bisa dilakukan jika ada <strong>Periode/Semester Aktif</strong>.</li>
                        <li>SDM masuk ke menu Penilaian, pilih aksi <strong>Kelola Periode</strong>.</li>
                        <li>Isi Tanggal Mulai, Tanggal Selesai, aktifkan status, lalu klik <strong>Buat Periode</strong>.</li>
                    </ul>
                </div>

                <div class="rounded-lg border border-emerald-200 bg-emerald-50/50 p-4 dark:border-emerald-800/50 dark:bg-emerald-900/20">
                    <h4 class="font-bold text-emerald-700 dark:text-emerald-400 mb-2 flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-200 text-emerald-800 dark:bg-emerald-800 dark:text-emerald-200 text-xs">2</span>
                        Input Capaian oleh Pegawai
                    </h4>
                    <ul class="list-disc pl-5 mt-1 space-y-1">
                        <li>Semua pegawai masuk ke menu <strong>Penilaian / Pelatihan Pegawai</strong>.</li>
                        <li>Klik action <strong>Tambah Capaian</strong>.</li>
                        <li>Isi Judul dan Deskripsi kegiatan/capaian Anda selama periode tersebut, lalu klik <strong>Simpan</strong>.</li>
                    </ul>
                </div>

                <div class="rounded-lg border border-indigo-200 bg-indigo-50/50 p-4 dark:border-indigo-800/50 dark:bg-indigo-900/20">
                    <h4 class="font-bold text-indigo-700 dark:text-indigo-400 mb-2 flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-200 text-indigo-800 dark:bg-indigo-800 dark:text-indigo-200 text-xs">3</span>
                        Proses Penilaian Berjenjang (Atasan)
                    </h4>
                    <p class="mb-2">Setelah capaian diinput, sistem akan meneruskannya ke Atasan secara berjenjang berdasarkan kedudukan jabatan Anda saat ini:</p>
                    
                    <div class="bg-white dark:bg-gray-900 rounded border border-indigo-100 dark:border-indigo-900 p-3 mb-3 text-xs">
                        <ul class="space-y-2">
                            <li class="flex items-start gap-2">
                                <span class="font-semibold w-32 shrink-0">Staf Pelaksana:</span>
                                <span>Dinilai bertahap oleh: Kepala Unit ➔ Koordinator ➔ Kepala Seksi ➔ Direktur</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="font-semibold w-32 shrink-0 text-gray-500">Staf Tanpa Kanit:</span>
                                <span class="text-gray-500">Dinilai bertahap oleh: Koordinator ➔ Kepala Seksi ➔ Direktur</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="font-semibold w-32 shrink-0">Kepala Unit:</span>
                                <span>Dinilai bertahap oleh: Koordinator ➔ Kepala Seksi ➔ Direktur</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="font-semibold w-32 shrink-0">Koordinator:</span>
                                <span>Dinilai bertahap oleh: Kepala Seksi ➔ Direktur</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="font-semibold w-32 shrink-0">Kepala Seksi:</span>
                                <span>Dinilai langsung oleh: Direktur</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="font-semibold w-32 shrink-0">Direktur:</span>
                                <span>Ditinjau oleh: SDM (Melalui tab Perlu Tinjauan)</span>
                            </li>
                        </ul>
                    </div>

                    <p class="font-semibold text-indigo-800 dark:text-indigo-300 mt-3 mb-1">Cara Melakukan Penilaian (Bagi Atasan):</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Masuk ke menu Penilaian dan buka tab <strong>Perlu Penilaian</strong>.</li>
                        <li>Klik cell <strong>Nilai Pegawai</strong> pada data yang perlu dinilai.</li>
                        <li>Sebuah modal akan muncul. Isi <strong>Nilai</strong> dan berikan <strong>Catatan</strong> (Opsional).</li>
                        <li>Klik <strong>Simpan</strong>. Data otomatis diteruskan ke tingkat atasnya hingga mencapai Direktur untuk Penilaian Akhir.</li>
                    </ul>
                </div>

            </div>
        '));
}

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Capaian')
                ->createAnother(false)
                ->visible(fn() => (!$this->activeTab || $this->activeTab == 'sendiri') && (function(){
                    $period = PerformancePeriod::where('status', 1)->latest()->value('id');
                    $performance = StaffPerformance::where('period_id', $period)->where('staff_id', Auth::user()->staff_id)->first();

                    if ($performance) return false;
                    return true;
                })()),
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
                        ->label('Capaian')
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
                                            1 => in_array($record->appraisal->appraiser->chair->level, [1,2]) && $record->staff->chair->level != 4 ? false : true,
                                        };
                                    } else {
                                        if (Auth::user()->staff->chair->level == 4){
                                            if ($record->staff->chair->level == 1 && Auth::user()->role_id == 1) return true;
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
                    TextColumn::make('appraiser')
                        ->label('Telah Dinilai Oleh')
                        ->state(function (StaffPerformance $record) {
                            if (!$record->appraisal) {
                                return '-';
                            } 
                            
                            return match ($record->appraisal->appraiser->chair->level) {
                                4 => 'Assesor Tingkat 1',
                                3 => 'Assesor Tingkat 2',
                                2 => 'Assesor Tingkat 3',
                                1 => 'Assesor Tingkat 4',
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
                        ->indicateUsing(function (array $data) {
                            $period = PerformancePeriod::find($data['value']);
                            $start = Carbon::parse($period->start_date)->translatedFormat('F');
                            $end = Carbon::parse($period->end_date)->translatedFormat('F Y');
                            return [
                                Indicator::make("Periode: $start - $end")
                                    ->removable(false),
                            ];
                        })
                        ->default(function () {
                            return PerformancePeriod::where('status', true)->latest()->first()?->id;
                        })
                        ->searchable()
                        ->selectablePlaceholder(false)
                        ->native(false),
                ])
                ->contentFooter(view('filament.tables.avgscore-pagination', [
                    'score' => $this->averageScore,
                ]))
                ->recordActions([
                    Action::make('approve')
                        ->label('Setujui Nilai')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->visible(fn ($record) => $record->appraisal?->appraiser->chair->level > Auth::user()->staff->chair->level)
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
                        ->label('Capaian')
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
                        }),
                    TextColumn::make('appraiser')
                        ->label('Telah Dinilai Oleh')
                        ->state(function (StaffPerformance $record) {
                            if (!$record->appraisal) {
                                return '-';
                            } 
                            
                            return match ($record->appraisal->appraiser->chair->level) {
                                4 => 'Assesor Tingkat 1',
                                3 => 'Assesor Tingkat 2',
                                2 => 'Assesor Tingkat 3',
                                1 => 'Assesor Tingkat 4',
                                default => false,
                            };
                        })
                ])
                ->recordActions([
                    ViewAction::make(),
                    EditAction::make(),
                ])
                ->toolbarActions([
                    BulkActionGroup::make([
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
