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
use Illuminate\Support\Str;
use Mpdf\Mpdf;

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
    public ?string $pdfToken = null;
    
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

    public function getHeading(): string | Htmlable
    {
        $unitName = $this->record->name;
        $isOrdinaryStaff = Auth::user()->staff->chair->level == 4 && Auth::user()->staff->chair_id != Auth::user()->staff->unit->leader_id;
        $infoIcon = '<svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                 </svg>';

        if ($isOrdinaryStaff) {
            $button = '<button 
                type="button" 
                wire:click="mountAction(\'infoExchange\')" 
                class="text-primary-500 hover:text-primary-600 transition focus:outline-none" 
                title="Lihat Panduan Tukar Jadwal">
                ' . $infoIcon . '
            </button>';
        } else {
            $button = '<button 
                type="button" 
                wire:click="mountAction(\'infoAction\')" 
                class="text-primary-500 hover:text-primary-600 transition focus:outline-none" 
                title="Lihat Panduan Penjadwalan">
                ' . $infoIcon . '
            </button>';
        }

        return new HtmlString(<<<HTML
            <div class="flex items-center gap-x-2">
                <span>Jadwal Unit: {$unitName}</span> 
                
                {$button}
            </div>
        HTML);
    }

    public function infoAction(): Action
    {
        return Action::make('info')
            ->modalHeading('Panduan Pengelolaan Jadwal & Shift')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalWidth('3xl')
            ->modalContent(fn () => new HtmlString('
                <div class="text-sm text-gray-700 dark:text-gray-300 space-y-4">
                    <p>Berikut adalah prosedur pembuatan jadwal shift kerja bulanan untuk pegawai di unit masing-masing:</p>
                    
                    <div class="rounded-lg border border-slate-200 bg-slate-50/50 p-4 dark:border-slate-700/50 dark:bg-slate-800/20">
                        <h4 class="font-bold text-slate-700 dark:text-slate-300 mb-2 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-200 text-xs">1</span>
                            Akses Menu Penjadwalan
                        </h4>
                        <p class="mb-2">Akses menu jadwal disesuaikan dengan kedudukan/role Anda saat login:</p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li><span class="font-semibold">Kepala Unit:</span> Langsung masuk ke menu <strong>Jadwal Unit</strong>.</li>
                            <li><span class="font-semibold">Koordinator / SDM:</span> Masuk ke menu <strong>Unit Kerja</strong>, lalu pilih action <strong>Jadwal</strong> pada baris nama unit yang ingin dikelola.</li>
                        </ul>
                        <p class="mt-2 text-xs text-gray-500">Keduanya akan menampilkan halaman kalender yang berisi daftar nama anggota unit beserta jadwal dalam 1 bulan.</p>
                    </div>

                    <div class="rounded-lg border border-amber-200 bg-amber-50/50 p-4 dark:border-amber-800/50 dark:bg-amber-900/20">
                        <h4 class="font-bold text-amber-700 dark:text-amber-400 mb-2 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-amber-200 text-amber-800 dark:bg-amber-800 dark:text-amber-200 text-xs">2</span>
                            Pengecekan & Pembuatan Shift
                        </h4>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Sistem akan mendeteksi apakah unit Anda sudah memiliki data Shift atau belum.</li>
                            <li><strong>Jika Belum Ada:</strong> Anda wajib membuatnya terlebih dahulu. Klik tombol <strong>Kelola Shift</strong>, lalu isi Nama Shift, Kode, Jam Masuk, dan Jam Pulang. Klik <strong>Kirim</strong> untuk menyimpan master shift.</li>
                            <li><strong>Jika Sudah Ada:</strong> Lanjut ke tahap pengisian jadwal (Tahap 3).</li>
                        </ul>
                    </div>

                    <div class="rounded-lg border border-emerald-200 bg-emerald-50/50 p-4 dark:border-emerald-800/50 dark:bg-emerald-900/20">
                        <h4 class="font-bold text-emerald-700 dark:text-emerald-400 mb-2 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-200 text-emerald-800 dark:bg-emerald-800 dark:text-emerald-200 text-xs">3</span>
                            Pembuatan Jadwal Pegawai
                        </h4>
                        <p class="mb-3">Terdapat 2 cara untuk mengisi jadwal pegawai. Keduanya akan <strong>otomatis tersimpan</strong> dan jam kerja akan <strong>terakumulasi</strong> secara instan:</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-white dark:bg-gray-900 rounded border border-emerald-100 dark:border-emerald-900/50 p-3">
                                <p class="font-semibold text-emerald-700 dark:text-emerald-400 mb-1 border-b pb-1">Cara Manual</p>
                                <ul class="list-decimal pl-4 mt-1 space-y-1 text-xs">
                                    <li>Pilih Filter <strong>Bulan dan Tahun</strong> di bagian atas.</li>
                                    <li>Arahkan kursor ke baris nama karyawan.</li>
                                    <li>Klik pada <em>input</em> tanggal tertentu yang ingin diatur.</li>
                                    <li>Pilih <strong>Shift</strong> yang sesuai untuk hari tersebut.</li>
                                </ul>
                            </div>
                            
                            <div class="bg-white dark:bg-gray-900 rounded border border-emerald-100 dark:border-emerald-900/50 p-3">
                                <p class="font-semibold text-emerald-700 dark:text-emerald-400 mb-1 border-b pb-1">Cara Generate (Otomatis)</p>
                                <ul class="list-decimal pl-4 mt-1 space-y-1 text-xs">
                                    <li>Klik tombol <strong>Generate</strong>.</li>
                                    <li>Sebuah modal akan muncul, pilih <strong>Bulan dan Tahun</strong> target jadwal.</li>
                                    <li>Klik <strong>Generate Jadwal</strong>. Sistem akan menyusun jadwal secara otomatis berdasarkan pola yang diatur sebelumnya.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            '));
    }

    public function infoExchange(): Action
    {
        return Action::make('info')
            ->modalHeading('Panduan Pengajuan Tukar Jadwal')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalWidth('2xl')
            ->modalContent(fn () => new HtmlString('
                <div class="text-sm text-gray-700 dark:text-gray-300 space-y-4">
                    <p>Berikut adalah prosedur persetujuan penukaran jadwal kerja antar pegawai</p>
                    
                    <div class="rounded-lg border border-slate-200 bg-slate-50/50 p-4 dark:border-slate-700/50 dark:bg-slate-800/20">
                        <h4 class="font-bold text-slate-700 dark:text-slate-300 mb-2 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-200 text-xs">1</span>
                            Syarat Pendahuluan
                        </h4>
                        <ul class="list-disc pl-5 mt-2 space-y-1">
                            <li>Pastikan Anda sudah memiliki jadwal kerja pada tanggal yang ingin ditukar.</li>
                            <li>Pastikan Anda sudah berkomunikasi secara personal dan mendapat izin dari rekan kerja yang akan dijadikan pengganti. Jika syarat ini tidak terpenuhi, pengajuan tidak disarankan.</li>
                        </ul>
                    </div>

                    <div class="rounded-lg border border-emerald-200 bg-emerald-50/50 p-4 dark:border-emerald-800/50 dark:bg-emerald-900/20">
                        <h4 class="font-bold text-emerald-700 dark:text-emerald-400 mb-2 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-200 text-emerald-800 dark:bg-emerald-800 dark:text-emerald-200 text-xs">2</span>
                            Pengajuan oleh Pegawai
                        </h4>
                        <ul class="list-disc pl-5 mt-2 space-y-1">
                            <li>Masuk ke menu <strong>Jadwal Unit</strong>.</li>
                            <li>Pilih aksi <strong>Tukar Jadwal</strong> pada jadwal yang ingin Anda ubah.</li>
                            <li>Isi formulir pengajuan secara lengkap meliputi Tanggal Tukar, Nama Rekan Pengganti, dan Alasan pertukaran.</li>
                            <li>Klik tombol <strong>Tukar Jadwal</strong>. Sistem akan mengirimkan notifikasi pengajuan ke Kepala Unit / Koordinator Anda.</li>
                            <li>Pengajuan masih bersifat menunggu persetujuan. Baik nanti disetujui atau tidak, pegawai akan mendapatkan notifikasi serta jadwal akan tertukar secara otomatis apabila disetujui.</li>
                        </ul>
                    </div>

                </div>
            '));
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportPDF')
                ->label('Cetak Jadwal')
                ->icon('heroicon-o-printer')
                ->color('primary')
                ->modalHeading('Preview Jadwal')
                ->modalWidth('7xl') 
                ->modalContent(function ($livewire) {
                    $month = $livewire->tableFilters['month']['value'] ?? now()->month;
                    $year = $livewire->tableFilters['year']['value'] ?? now()->year;

                    $unit = Unit::find($this->record->id);

                    $startDate = Carbon::create($year, $month, 1); 
                    $daysInMonth = $startDate->daysInMonth;
                    
                    $periodName = $startDate->locale('id')->translatedFormat('F Y');
                    
                    $dates = [];
                    for ($i = 1; $i <= $daysInMonth; $i++) {
                        $currentDate = $startDate->copy()->addDays($i - 1);
                        $dates[] = [
                            'tanggal' => $currentDate->format('d'),
                            'hari' => $currentDate->translatedFormat('D'), 
                            'full_date' => $currentDate->format('Y-m-d')
                        ];
                    }

                    $staffs = Staff::where('unit_id', $unit->id)->get();
                    
                    if ($staffs->isEmpty()) {
                        return view('filament.components.alert', [
                            'message' => 'Tidak ada pegawai di unit terpilih.',
                            'color'   => 'warning',
                        ]);
                    }

                    $schedules = Schedule::with('shift')
                        ->whereIn('staff_id', $staffs->pluck('id'))
                        ->whereMonth('schedule_date', $month)
                        ->whereYear('schedule_date', $year)
                        ->get()
                        ->groupBy('staff_id');

                    $shifts = Shift::where('unit_id', $unit->id)->get()->keyBy('id');

                    $html = view('exports.schedules', [
                        'unit' => $unit,
                        'periodName' => $periodName,
                        'dates' => $dates,
                        'staffs' => $staffs,
                        'schedules' => $schedules,
                        'shifts' => $shifts
                    ])->render();

                    $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
                    $fontDirs = $defaultConfig['fontDir'];
                    $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
                    $fontData = $defaultFontConfig['fontdata'];

                    $mpdf = new Mpdf([
                        'mode' => 'utf-8', 
                        'orientation' => 'L', 
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
                        'margin_left' => 10, 
                        'margin_right' => 10,
                        'margin_bottom' => 15,
                    ]);

                    $mpdf->WriteHTML($html);

                    $token = Str::uuid()->toString();
                    $pdfPath = storage_path("app/private/livewire-tmp/$token.pdf");
                    file_put_contents($pdfPath, $mpdf->Output('', 'S'));
                    $livewire->pdfToken = $token;

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
                                <div class='p-2 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700'>
                                    <span class='font-bold text-primary-600'>{$shiftName}</span> <br> 
                                    <span class='text-sm text-gray-500'>($time)</span>
                                </div>
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
                                        <span class='font-bold text-warning-600'>{$shiftName}</span><br>
                                        <span class='text-sm text-gray-500'>($time)</span>
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
                    <span class='text-[10px] font-bold text-gray-400 uppercase tracking-wide mt-1'>
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
