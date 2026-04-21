<?php

namespace App\Filament\Resources\Presences\Pages;

use App\Exports\PresenceExport;
use App\Filament\Resources\Presences\PresenceResource;
use App\Livewire\DeviceCaptureWidget;
use App\Models\MonthlyPeriod;
use App\Models\Presence;
use App\Models\Schedule;
use App\Models\Staff;
use App\Models\Unit;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;

class ManagePresences extends ManageRecords implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = PresenceResource::class;
    
    public ?string $pdfToken = null;

    public function mount(): void
    {
        if (request()->has('activeTab')) {
            $this->activeTab = request()->query('activeTab');
        }

        $requestedTab = request()->query('tab') ?? request()->query('activeTab');

        if ($requestedTab === 'karyawan') {
            $user = Auth::user();
            $isBoss = $user->staff->chair->level == 1 || $user->role_id == 1;

            if (! $isBoss) {
                $this->redirect($this->getResource()::getUrl('index'));
                return;
            }
        }

        parent::mount();
    }

    public function getHeading(): string | Htmlable
    {
        return new HtmlString(<<<HTML
            <div class="flex items-center gap-x-2">
                <span>Presensi</span>
                
                <button 
                    type="button" 
                    wire:click="mountAction('infoAction')" 
                    class="text-primary-500 hover:text-primary-600 transition focus:outline-none" 
                    title="Lihat Panduan Presensi"
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
            ->modalHeading('Panduan Presensi (Check In / Check Out)')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalWidth('2xl')
            ->modalContent(fn () => new HtmlString('
                <div class="text-sm text-gray-700 dark:text-gray-300 space-y-4">
                    <p>Berikut adalah panduan lengkap cara melakukan presensi kehadiran harian melalui sistem:</p>

                    <div class="rounded-lg border border-slate-200 bg-slate-50/50 p-4 dark:border-slate-700/50 dark:bg-slate-800/20">
                        <h4 class="font-bold text-slate-700 dark:text-slate-300 mb-2 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-200 text-xs">1</span>
                            Persiapan & Akses Menu
                        </h4>
                        <ul class="list-disc pl-5 mt-2 space-y-1">
                            <li>Pastikan Anda <strong>memiliki jadwal kerja</strong> pada hari ini. (Jadwal dapat dilihat tepat di bawah tulisan Dashboard). Jika tidak ada jadwal, Anda tidak bisa presensi.</li>
                            <li>Pilih jalur presensi yang Anda inginkan:
                                <ul class="list-none pl-2 mt-1 space-y-1">
                                    <li><span class="font-medium text-slate-600 dark:text-slate-400">Jalur Cepat:</span> Langsung klik tombol <strong>Presensi</strong> yang ada di halaman Dashboard.</li>
                                    <li><span class="font-medium text-slate-600 dark:text-slate-400">Jalur Utama:</span> Buka menu navigasi <strong>Presensi</strong> di bilah samping <em>(sidebar)</em>.</li>
                                </ul>
                            </li>
                        </ul>
                    </div>

                    <p class="font-semibold text-gray-800 dark:text-gray-200 pt-1">Langkah 2: Pilih Metode Presensi</p>

                    <div class="rounded-lg border border-blue-200 bg-blue-50/50 p-4 dark:border-blue-800/50 dark:bg-blue-900/20">
                        <h4 class="font-bold text-blue-700 dark:text-blue-400 mb-2 flex items-center gap-2">
                            Opsi A: Menggunakan Jaringan Kantor
                        </h4>
                        <ul class="list-disc pl-5 mt-2 space-y-1">
                            <li>Jika Anda hendak <strong>Menggunakan Jaringan</strong>, klik tombol <strong>Check In / Check Out</strong>.</li>
                            <li><strong>Syarat Mutlak:</strong> Perangkat (HP/Laptop) Anda <u>wajib</u> terhubung dengan jaringan internet/Wi-Fi resmi milik Rumah Sakit.</li>
                            <li>Jika Anda menggunakan paket data pribadi atau Wi-Fi luar, sistem akan menolak presensi dengan peringatan <em>"Gunakan Jaringan Kantor"</em>.</li>
                        </ul>
                    </div>

                    <div class="rounded-lg border border-emerald-200 bg-emerald-50/50 p-4 dark:border-emerald-800/50 dark:bg-emerald-900/20">
                        <h4 class="font-bold text-emerald-700 dark:text-emerald-400 mb-2 flex items-center gap-2">
                            Opsi B: Menggunakan Geolokasi (GPS)
                        </h4>
                        <ul class="list-disc pl-5 mt-2 space-y-1">
                            <li>Jika Anda hendak <strong>Menggunakan Geolokasi</strong>, klik <strong>Check In dengan GPS</strong>.</li>
                            <li>Tunggu maksimal 15 detik agar sistem dapat mendeteksi dan mengunci lokasi satelit Anda dengan akurat.</li>
                            <li><strong>Ketentuan Verifikasi Lokasi:</strong>
                                <ul class="list-none pl-2 mt-2 space-y-2 text-xs">
                                    <li class="flex gap-2 items-start"><span class="text-red-500 font-bold">✗</span> <span><strong>Anti Fake GPS:</strong> Sistem dilengkapi pendeteksi lokasi palsu. Jika Anda menggunakan aplikasi Fake GPS, sistem akan menampilkan peringatan dan mengunci presensi.</span></li>
                                    <li class="flex gap-2 items-start"><span class="text-amber-500 font-bold">!</span> <span><strong>Jarak > 200m (Di luar jangkauan):</strong> Jika lokasi Anda berada lebih dari 200 meter dari titik koordinat RS atau akurasi sinyal lemah, tombol presensi tidak akan muncul. Silakan mendekat ke area RS atau cari tempat terbuka.</span></li>
                                    <li class="flex gap-2 items-start"><span class="text-emerald-500 font-bold">✓</span> <span><strong>Jarak < 200m (Dalam jangkauan):</strong> Sistem akan menampilkan indikator jarak Anda. Tombol <strong>Check In / Check Out</strong> akan aktif dan bisa ditekan untuk merekam presensi.</span></li>
                                </ul>
                            </li>
                        </ul>
                    </div>

                </div>
            '));
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DeviceCaptureWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ip-status')
                ->label('IP Info')
                ->color('gray')
                ->icon('heroicon-o-signal')
                ->modalHeading('Status Jaringan')
                ->modalWidth('md')
                ->modalContent(view('filament.components.current-ip')),
            Action::make('check_in')
                ->label('Check In')
                ->icon('heroicon-o-finger-print')
                ->visible(function () {
                    $scheduleToday = Schedule::where('staff_id', Auth::user()->staff_id)
                            ->where('schedule_date', now()->toDateString())
                            ->first();
                        $isOff = $scheduleToday && $scheduleToday->shift->is_off == 1 ? true : false;

                    $latestPresence = Presence::where('staff_id', Auth::user()->staff_id)
                        ->latest('presence_date')
                        ->first();
                    $isAlreadyCheckedOut = $latestPresence ? $latestPresence->check_out !== null : true;

                    $isNotPresenceYet = Presence::where('staff_id', Auth::user()->staff_id)
                        ->whereDate('presence_date', now()->toDateString())
                        ->doesntExist();

                    return $isAlreadyCheckedOut && $isNotPresenceYet && !$isOff;
                })
                ->action(function () {
                    $device = session('device_info');
                    $today = now()->toDateString();
                    $settingIP = setting('ip');
                    $allowedIPs = array_map('trim', explode(';', $settingIP));

                    if (!$device) {
                        Notification::make()
                            ->title('Data perangkat belum terdeteksi!')
                            ->danger()
                            ->send();
                        return;
                    }

                    if (!Str::startsWith($device['ip'], $allowedIPs)) {
                        Notification::make()
                            ->title('Hubungkan dengan jaringan kantor!')
                            ->danger()
                            ->send();
                        return;
                    }

                    $sameDeviceToday = Presence::where('fingerprint', $device['device_id'])
                        ->whereDate('presence_date', $today)
                        ->exists();

                    if ($sameDeviceToday) {
                        Notification::make()
                            ->title('Perangkat telah digunakan untuk check-in hari ini!')
                            ->danger()
                            ->send();
                        return;
                    }

                    $data = [
                        'staff_id' => Auth::user()->staff_id,
                        'presence_date' => now()->toDateString(),
                        'check_in' => now()->toTimeString(),
                        'method' => 'network',
                        'ip' => $device['ip'],
                        'fingerprint' => $device['device_id'],
                    ];

                    Presence::create($data);

                    Notification::make()
                        ->title('Check-in berhasil!')
                        ->success()
                        ->send();
                }),
            Action::make('check_out')
                ->label('Check Out')
                ->icon('heroicon-o-finger-print')
                ->visible(function () {
                    return Presence::where('staff_id', Auth::user()->staff_id)
                        ->whereNull('check_out')
                        ->exists();
                })
                ->action(function () {
                    $presence = Presence::where('staff_id', Auth::user()->staff_id)->whereNull('check_out')->latest('presence_date')->first();
                    $presence->check_out = now()->toTimeString();
                    $presence->save();

                    Notification::make()
                        ->title('Check-out berhasil!')
                        ->success()
                        ->send();
                }),
            Action::make('check_in_gps')
                ->label('Check In dengan GPS')
                ->icon('heroicon-o-map-pin')
                ->color('info')
                ->visible(function () {
                    $scheduleToday = Schedule::where('staff_id', Auth::user()->staff_id)
                            ->where('schedule_date', now()->toDateString())
                            ->first();
                    $isOff = $scheduleToday && $scheduleToday->shift->is_off == 1 ? true : false;


                    $latestPresence = Presence::where('staff_id', Auth::user()->staff_id)
                        ->latest('presence_date')
                        ->first();
                    $isAlreadyCheckedOut = $latestPresence ? $latestPresence->check_out !== null : true;

                    $isNotPresenceYet = Presence::where('staff_id', Auth::user()->staff_id)
                        ->whereDate('presence_date', now()->toDateString())
                        ->doesntExist();

                    return $isAlreadyCheckedOut && $isNotPresenceYet && !$isOff;
                })
                ->modalHeading('Absensi via Koordinat Lokasi')
                ->modalWidth('2xl')
                ->modalSubmitAction(false)
                ->modalCancelAction(false)
                ->modalContent(fn () => view('filament.components.map-modal')),
            Action::make('check_out_gps')
                ->label('Check Out dengan GPS')
                ->icon('heroicon-o-map-pin')
                ->color('info')
                ->visible(function () {
                    return Presence::where('staff_id', Auth::user()->staff_id)
                        ->whereNull('check_out')
                        ->exists();
                })
                ->modalHeading('Absensi via Koordinat Lokasi')
                ->modalWidth('2xl')
                ->modalSubmitAction(false)
                ->modalCancelAction(false)
                ->modalContent(fn () => view('filament.components.map-modal')),
            Action::make('periods')
                ->label('Kelola Periode')
                ->modalHeading('Manajemen Periode Bulanan')
                ->modalContent(view('filament.pages.partials.monthly-period-manager-modal')) 
                ->modalSubmitAction(false) 
                ->modalCancelAction(false)
                ->modalWidth('xl')
                ->icon('heroicon-o-swatch')
                ->color('gray')
                ->visible(fn() => Auth::user()->role_id == 1)
                ->slideOver(),
        ];
    }

    public function getSubheading(): string|Htmlable|null
    {
        $schedule = Schedule::where('staff_id', Auth::user()->staff_id)
                        ->whereDate('schedule_date', Carbon::now())
                        ->first();

        if (!$schedule) return null;

        $shift = $schedule->shift;

        $start = Carbon::parse($shift->start_time ?? '00:00:00')->format('H:i');
        $end   = Carbon::parse($shift->end_time ?? '00:00:00')->format('H:i');

        $shiftItem = "
            <div class='flex items-center gap-1 whitespace-nowrap bg-gray-100 dark:bg-white/5 px-2 py-1 rounded-md border border-gray-200 dark:border-white/10'>
                <span class='font-bold text-primary-600 dark:text-primary-400'>Jadwal Hari ini:</span>
                <span class='text-gray-700 dark:text-gray-300'>{$start}-{$end} ($shift->code)</span>
            </div>
        ";

        return new HtmlString("
            <div class='flex flex-wrap items-center gap-2 mt-2 text-xs'>
                <div class='flex items-center justify-center w-6 h-6 bg-gray-100 dark:bg-gray-800 rounded-full shrink-0'>
                    <svg class='w-4 h-4 text-gray-500' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor'>
                        <path fill-rule='evenodd' d='M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z' clip-rule='evenodd' />
                    </svg>
                </div>
                
                {$shiftItem}
            </div>
        ");
    }

    public function getTabs(): array
    {
        $user = Auth::user();
        $user->staff_id = $user->staff_id ?? 1;

        $tabs = [];
        
        if ($user->role_id == 1){
            $tabs['sendiri'] = Tab::make('Presensi Saya')
                ->icon('heroicon-o-inbox');
            $tabs['karyawan'] = Tab::make("Presensi Karyawan")
                ->icon('heroicon-o-inbox-stack');
        }

        return $tabs;
    }

    public function table(Table $table): Table
    {
        $activeTab = $this->activeTab ?? 'sendiri';

        if ($activeTab == 'sendiri') {
            return $table
                ->recordTitleAttribute('Presence')
                ->query(function (): Builder {
                    return Presence::where('staff_id', Auth::user()->staff_id);
                })
                ->headerActions([
                    ActionGroup::make([
                        Action::make('exportPdf')
                            ->label('Export PDF')
                            ->icon('heroicon-o-document-arrow-down')
                            ->color('warning')
                            ->modalHeading('Preview Cuti')
                            ->modalWidth('5xl')
                            ->modalContent(function ($livewire) {
                                $periodId = $livewire->tableFilters['period_id']['value'] ?? null;

                                if ($periodId) {
                                    $period = MonthlyPeriod::find($periodId);
                                } else {
                                    $period = MonthlyPeriod::whereDate('start_date', '<=', now())
                                        ->whereDate('end_date', '>=', now())
                                        ->first();
                                }

                                if (!$period) {
                                    return view('filament.components.alert', [
                                        'message' => 'Periode Presensi Tidak Ditemukan.',
                                        'color'   => 'danger',
                                    ]);
                                }
                                $presences = Presence::query()
                                    ->with(['staff.chair', 'staff.unit'])
                                    ->where('staff_id', Auth::user()->staff_id)
                                    ->whereDate('presence_date', '>=', $period->start_date)
                                    ->whereDate('presence_date', '<=', $period->end_date)
                                    ->orderBy('presence_date')
                                    ->get();

                                $schedules = Schedule::with('shift')
                                    ->where('staff_id', Auth::user()->staff_id)
                                    ->whereDate('schedule_date', '>=', $period->start_date)
                                    ->whereDate('schedule_date', '<=', $period->end_date)
                                    ->get()
                                    ->keyBy('schedule_date'); 

                                if ($schedules->isEmpty()) {
                                    return view('filament.components.alert', [
                                        'message' => 'Jadwal pada periode tersebut belum dibuat!',
                                        'color'   => 'danger',
                                    ]);
                                }

                                if ($presences->isEmpty()) {
                                    return view('filament.components.alert', [
                                        'message' => 'Belum ada presensi pada periode tersebut!',
                                        'color'   => 'warning',
                                    ]);
                                }

                                $role = Auth::user()->role_id;

                                $html = view('exports.presences', [
                                    'data' => $presences,
                                    'schedules' => $schedules,
                                    'month' => $period->name,
                                    'role' => $role
                                ])->render();

                                $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
                                $fontDirs = $defaultConfig['fontDir'];

                                $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
                                $fontData = $defaultFontConfig['fontdata'];

                                $mpdf = new Mpdf([
                                    'mode' => 'utf-8', 
                                    'format' => [215.9, 342.9],
                                    'fontDir' => array_merge($fontDirs, [
                                        public_path('fonts'), 
                                    ]),
                                    'fontdata' => $fontData + [
                                        'tnr' => [
                                            'R' => 'times.ttf',    
                                            'B' => 'timesbd.ttf',  
                                            'I' => 'timesi.ttf',   
                                            'BI' => 'timesbi.ttf',  
                                        ]
                                    ],
                                    'default_font' => 'tnr',
                                    'margin_top' => 15,
                                    'margin_left' => 20,
                                    'margin_right' => 20,
                                    'margin_bottom' => 15,
                                ]);

                                $mpdf->WriteHTML($html);

                                $token = Str::uuid()->toString();
                                $pdfPath = storage_path("app/private/livewire-tmp/$token.pdf");

                                file_put_contents($pdfPath, $mpdf->Output('', 'S'));

                                $this->pdfToken = $token;

                                return view('filament.components.preview-pdf', [
                                    'token' => $token,
                                ]);
                            })
                            ->modalHeading(false)
                            ->modalCancelAction(false)
                            ->modalSubmitAction(false)
                            ->modalCloseButton(false)
                            ->closeModalByClickingAway(false)
                            ->closeModalByEscaping(false)
                            ->extraAttributes([
                                'x-on:click.capture' => 'close()'
                            ]),
                        Action::make('exportWord')
                            ->label('Export Word')
                            ->icon('heroicon-o-document-text')
                            ->color('info')
                            ->action(function ($livewire) {
                                $periodId = $livewire->tableFilters['period_id']['value'] ?? null;

                                if ($periodId) {
                                    $period = MonthlyPeriod::find($periodId);
                                } else {
                                    $period = MonthlyPeriod::whereDate('start_date', '<=', now())
                                        ->whereDate('end_date', '>=', now())
                                        ->first();
                                }

                                if (!$period) {
                                    Notification::make()
                                        ->title('Periode presensi tidak ditemukan!')
                                        ->danger()
                                        ->send();
                                    return;
                                }

                                $presences = Presence::query()
                                    ->with(['staff.chair', 'staff.unit'])
                                    ->where('staff_id', Auth::user()->staff_id)
                                    ->whereDate('presence_date', '>=', $period->start_date)
                                    ->whereDate('presence_date', '<=', $period->end_date)
                                    ->orderBy('presence_date')
                                    ->get();

                                $schedules = Schedule::with('shift')
                                    ->where('staff_id', Auth::user()->staff_id)
                                    ->whereDate('schedule_date', '>=', $period->start_date)
                                    ->whereDate('schedule_date', '<=', $period->end_date)
                                    ->get()
                                    ->keyBy('schedule_date'); 

                                if ($schedules->isEmpty()) {
                                    Notification::make()
                                        ->title('Jadwal pada periode tersebut belum dibuat!')
                                        ->danger()
                                        ->send();
                                    return;
                                }

                                if ($presences->isEmpty()) {
                                    Notification::make()
                                        ->title('Belum ada presensi pada periode tersebut!')
                                        ->danger()
                                        ->send();
                                    return;
                                }

                                $role = Auth::user()->role_id;
                                $name = Auth::user()->staff->name ?? 'Pegawai';
                                $html = view('exports.presences', [
                                    'data' => $presences,
                                    'schedules' => $schedules,
                                    'month' => $period->name,
                                    'role' => $role
                                ])->render();

                                $fileName = 'Presensi_' . $name . '_' . $period->name . '.doc';

                                return response()->streamDownload(function () use ($html) {
                                    echo '<meta charset="UTF-8">';
                                    echo $html;
                                }, $fileName, [
                                    'Content-Type' => 'application/vnd.ms-word',
                                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                                ]);
                            }),
                    ])
                    ->label('Export Data') 
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->button() 
                    ->visible(fn ($livewire) => $livewire->tableFilters['period_id']['value'])
                    ->color('success')
                    ->dropdownPlacement('bottom-end'),
                ])
                ->columns([
                    TextColumn::make('presence_date')
                        ->label('Tanggal')
                        ->date()
                        ->sortable(),
                    TextColumn::make('check_in')
                        ->label('Masuk')
                        ->time()
                        ->sortable(),
                    TextColumn::make('check_out')
                        ->label('Pulang')
                        ->time()
                        ->formatStateUsing(fn ($record) => $record->check_out < $record->check_in ? $record->check_out . ' (+1 Hari)' : $record->check_out)
                        ->sortable(),
                    TextColumn::make('method')
                        ->label('Metode Presensi')
                        ->formatStateUsing(fn ($state) => $state == 'network' ? 'Jaringan' : 'Lokasi')
                        ->sortable(),
                ])
                ->filters([
                    SelectFilter::make('period_id')
                        ->label('Periode Presensi')
                        ->options(function () {
                            return MonthlyPeriod::orderBy('start_date', 'desc')->pluck('name', 'id');
                        })
                        ->default(function () {
                            return MonthlyPeriod::where('start_date', '<=', now())
                                ->where('end_date', '>=', now())
                                ->value('id');
                        })
                        ->query(function (Builder $query, $data) {
                            if (empty($data['value'])) {
                                return $query;
                            }

                            $period = MonthlyPeriod::find($data['value']);

                            if ($period) {
                                $query->whereBetween('presence_date', [$period->start_date, $period->end_date]);
                            }
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
                ]);
        } else {
            $unit = $this->tableFilters['unit']['value'] ?? 0;
            $unit = $unit == 0 ? 1 : $unit;
            
            return $table
                ->headerActions([
                    Action::make('exportExcel')
                        ->label('Export Excel')
                        ->icon('heroicon-o-document-arrow-down')
                        ->visible(fn ($livewire) => $livewire->tableFilters['period_id']['value'])
                        ->color('primary')
                        ->action(function ($livewire) {
                            $periodId = $livewire->tableFilters['period_id']['value'] ?? null;
                            $period = MonthlyPeriod::find($periodId);

                            $export = new PresenceExport($period->start_date, $period->end_date);
                            return Excel::download($export, 'Rekap_Presensi_Periode_' . $period->name . '.xlsx');
                        }),
                ])
                ->recordTitleAttribute('name')
                ->query(Staff::query()->orderBy('unit_id'))
                ->columns([
                    TextColumn::make('no')
                    ->label('#')
                    ->rowIndex()
                    ->sortable(false)
                    ->toggleable(false)
                    ->width('80px'),
                    TextColumn::make('name')->label('Nama Pegawai')->sortable()->searchable(),
                    TextColumn::make('chair.name')->label('Jabatan'),
                ])
                ->filters([
                    SelectFilter::make('unit_id')
                        ->label('Unit')
                        ->options(fn() => Unit::pluck('name', 'id'))
                        ->default(fn() => Unit::first()?->id)
                        ->selectablePlaceholder(false) 
                        ->native(false)
                        ->searchable()
                        ->preload(),
                    SelectFilter::make('period_id')
                        ->label('Periode Presensi')
                        ->options(function () {
                            return MonthlyPeriod::orderBy('start_date', 'desc')->pluck('name', 'id');
                        })
                        ->default(function () {
                            return MonthlyPeriod::where('start_date', '<=', now())
                                ->where('end_date', '>=', now())
                                ->value('id');
                        })
                        ->query(function (Builder $query, $data) {
                            return $query;
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
                ])
                ->recordActions([
                    ActionGroup::make([
                        Action::make('exportPdf')
                            ->label('Export PDF')
                            ->icon('heroicon-o-document-arrow-down')
                            ->color('warning')
                            ->modalHeading('Preview Cuti')
                            ->modalWidth('5xl')
                            ->modalContent(function ($record, $livewire) {
                                $periodId = $livewire->tableFilters['period_id']['value'] ?? null;

                                if ($periodId) {
                                    $period = MonthlyPeriod::find($periodId);
                                } else {
                                    $period = MonthlyPeriod::whereDate('start_date', '<=', now())
                                        ->whereDate('end_date', '>=', now())
                                        ->first();
                                }

                                if (!$period) {
                                    return view('filament.components.alert', [
                                        'message' => 'Periode Presensi Tidak Ditemukan.',
                                        'color'   => 'danger',
                                    ]);
                                }
                                $presences = Presence::query()
                                    ->with(['staff.chair', 'staff.unit'])
                                    ->where('staff_id', $record->id) 
                                    ->whereDate('presence_date', '>=', $period->start_date)
                                    ->whereDate('presence_date', '<=', $period->end_date)
                                    ->orderBy('presence_date')
                                    ->get();

                                $schedules = Schedule::with('shift')
                                    ->where('staff_id', $record->id)
                                    ->whereDate('schedule_date', '>=', $period->start_date)
                                    ->whereDate('schedule_date', '<=', $period->end_date)
                                    ->get()
                                    ->keyBy('schedule_date'); 

                                if ($schedules->isEmpty()) {
                                    return view('filament.components.alert', [
                                        'message' => 'Jadwal pada periode tersebut belum dibuat!',
                                        'color'   => 'danger',
                                    ]);
                                }

                                if ($presences->isEmpty()) {
                                    return view('filament.components.alert', [
                                        'message' => 'Belum ada presensi pada periode tersebut!',
                                        'color'   => 'warning',
                                    ]);
                                }
                                
                                $role = Auth::user()->role_id;
                                $html = view('exports.presences', [
                                    'data' => $presences,
                                    'schedules' => $schedules, 
                                    'month' => $period->name,
                                    'role' => $role
                                ])->render();

                                $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
                                $fontDirs = $defaultConfig['fontDir'];

                                $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
                                $fontData = $defaultFontConfig['fontdata'];

                                $mpdf = new Mpdf([
                                    'mode' => 'utf-8', 
                                    'format' => [215.9, 342.9],
                                    'fontDir' => array_merge($fontDirs, [
                                        public_path('fonts'), 
                                    ]),
                                    'fontdata' => $fontData + [
                                        'tnr' => [
                                            'R' => 'times.ttf',    
                                            'B' => 'timesbd.ttf',  
                                            'I' => 'timesi.ttf',   
                                            'BI' => 'timesbi.ttf',  
                                        ]
                                    ],
                                    'default_font' => 'tnr',
                                    'margin_top' => 15,
                                    'margin_left' => 20,
                                    'margin_right' => 20,
                                    'margin_bottom' => 15,
                                ]);
                                $mpdf->WriteHTML($html);
                                
                                $token = Str::uuid()->toString();
                                $pdfPath = storage_path("app/private/livewire-tmp/$token.pdf");
                                file_put_contents($pdfPath, $mpdf->Output('', 'S'));
                                $this->pdfToken = $token;

                                return view('filament.components.preview-pdf', ['token' => $token]);
                            })
                            ->modalHeading(false)
                            ->modalCancelAction(false)
                            ->modalSubmitAction(false)
                            ->modalCloseButton(false)
                            ->closeModalByClickingAway(false)
                            ->closeModalByEscaping(false)
                            ->extraAttributes([
                                'x-on:click.capture' => 'close()'
                            ]),
                        Action::make('exportWord')
                            ->label('Export Word')
                            ->icon('heroicon-o-document-text')
                            ->color('info')
                            ->action(function ($record, $livewire) {
                                $periodId = $livewire->tableFilters['period_id']['value'] ?? null;

                                if ($periodId) {
                                    $period = MonthlyPeriod::find($periodId);
                                } else {
                                    $period = MonthlyPeriod::whereDate('start_date', '<=', now())
                                        ->whereDate('end_date', '>=', now())
                                        ->first();
                                }

                                if (!$period) {
                                    Notification::make()
                                        ->title('Periode presensi tidak ditemukan!')
                                        ->danger()
                                        ->send();
                                    return;
                                }

                                $presences = Presence::query()
                                    ->with(['staff.chair', 'staff.unit'])
                                    ->where('staff_id', $record->id) 
                                    ->whereDate('presence_date', '>=', $period->start_date)
                                    ->whereDate('presence_date', '<=', $period->end_date)
                                    ->orderBy('presence_date')
                                    ->get();

                                $schedules = Schedule::with('shift')
                                    ->where('staff_id', $record->id)
                                    ->whereDate('schedule_date', '>=', $period->start_date)
                                    ->whereDate('schedule_date', '<=', $period->end_date)
                                    ->get()
                                    ->keyBy('schedule_date'); 

                                if ($schedules->isEmpty()) {
                                    Notification::make()
                                        ->title('Jadwal pada periode tersebut belum dibuat!')
                                        ->danger()
                                        ->send();
                                    return;
                                }

                                if ($presences->isEmpty()) {
                                    Notification::make()
                                        ->title('Belum ada presensi pada periode tersebut!')
                                        ->danger()
                                        ->send();
                                    return;
                                }

                                $role = Auth::user()->role_id;
                                $name = $record->name ?? 'Pegawai';
                                $html = view('exports.presences', [
                                    'data' => $presences,
                                    'schedules' => $schedules,
                                    'month' => $period->name,
                                    'role' => $role
                                ])->render();

                                $fileName = 'Presensi_' . $name . '_' . $period->name . '.doc';

                                return response()->streamDownload(function () use ($html) {
                                    echo '<meta charset="UTF-8">';
                                    echo $html;
                                }, $fileName, [
                                    'Content-Type' => 'application/vnd.ms-word',
                                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                                ]);
                            }),
                        ])
                        ->label('Export Data')
                        ->link() 
                        ->visible(fn ($livewire) => $livewire->tableFilters['period_id']['value'])
                        ->icon('heroicon-m-arrow-top-right-on-square') 
                        ->color('success'),
                ]);
        }
    }

    public function updatedActiveTab(): void
    {
        parent::updatedActiveTab(); 
        $this->redirect(static::getResource()::getUrl('index', ['activeTab' => $this->activeTab]));
    }
    
    public function closePreviewAndCleanup() {
        if ($this->pdfToken) {
            $path = storage_path("app/private/livewire-tmp/{$this->pdfToken}.pdf");
            if (file_exists($path)) {
                @unlink($path);
            }
            $this->pdfToken = null;
        }

        $this->unmountAction();
    }
}
