<?php

namespace App\Filament\Resources\Presences\Pages;

use App\Filament\Resources\Presences\PresenceResource;
use App\Livewire\DeviceCaptureWidget;
use App\Models\Presence;
use App\Models\Schedule;
use App\Models\Staff;
use App\Models\Unit;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Mpdf\Mpdf;

class ManagePresences extends ManageRecords implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = PresenceResource::class;
    
    public ?string $pdfToken = null;

    public function mount(): void
    {
        // 1. Paksa isi variable activeTab dari URL Query String
        if (request()->has('activeTab')) {
            $this->activeTab = request()->query('activeTab');
        }

        parent::mount();
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
            // Action::make('ip-status')
            //     ->label('IP Info')
            //     ->color('gray')
            //     ->icon('heroicon-o-signal')
            //     ->modalHeading('Status Jaringan')
            //     ->modalWidth('md')
            //     ->modalContent(view('filament.components.current-ip')),
            Action::make('check_in')
                ->label('Check In')
                ->icon('heroicon-o-finger-print')
                ->visible(fn () => Presence::where('staff_id', Auth::user()->staff_id)->whereDate('presence_date', now()->toDateString())->count() == 0)
                ->action(function () {
                    $device = session('device_info');
                    $today = now()->toDateString();

                    if (!$device) {
                        Notification::make()
                            ->title('Data perangkat belum terdeteksi!')
                            ->danger()
                            ->send();
                        return;
                    }

                    if (substr($device['ip'], 0, 6) !== setting('ip')) {
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
                ->visible(fn () => Presence::where('staff_id', Auth::user()->staff_id)->whereDate('presence_date', now()->toDateString())->whereNull('check_out')->count() > 0)
                ->action(function () {
                    $today = now()->toDateString();
                    $presence = Presence::where('staff_id', Auth::user()->staff_id)->whereDate('presence_date', $today)->first();
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
                ->visible(fn () => Presence::where('staff_id', Auth::user()->staff_id)->whereDate('presence_date', now()->toDateString())->count() == 0)
                ->modalHeading('Absensi via Koordinat Lokasi')
                ->modalWidth('2xl')
                ->modalSubmitAction(false)
                ->modalCancelAction(false)
                ->modalContent(fn () => view('filament.components.map-modal')),
            Action::make('check_out_gps')
                ->label('Check Out dengan GPS')
                ->icon('heroicon-o-map-pin')
                ->color('info')
                ->visible(fn () => Presence::where('staff_id', Auth::user()->staff_id)->whereDate('presence_date', now()->toDateString())->whereNull('check_out')->count() > 0)
                ->modalHeading('Absensi via Koordinat Lokasi')
                ->modalWidth('2xl')
                ->modalSubmitAction(false)
                ->modalCancelAction(false)
                ->modalContent(fn () => view('filament.components.map-modal')),
                
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
                    Action::make('exportPdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('warning')
                    ->modalHeading('Preview Cuti')
                    ->modalWidth('5xl')
                    ->modalContent(function ($livewire) {
                        $month = $livewire->tableFilters['month_year']['value'] ?? now()->format('Y-m');

                        $data = Presence::query()
                            ->with(['staff.chair', 'staff.unit'])
                            ->where('staff_id', Auth::user()->staff_id)
                            ->whereMonth('presence_date', substr($month, 5, 2))
                            ->whereYear('presence_date', substr($month, 0, 4))
                            ->orderBy('presence_date')
                            ->get();

                            $role = Auth::user()->role_id;

                            $html = view('exports.presences', compact('data', 'month', 'role'))->render();

                        $mpdf = new Mpdf([
                            'mode' => 'utf-8',
                            'format' => 'A4',
                            'margin_left'   => 25, // 2.5 cm
                            'margin_right'  => 20, // 2 cm
                            'margin_top'    => 25, // 2.5 cm
                            'margin_bottom' => 20, // 2 cm
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
                    ->closeModalByEscaping(false),
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
                        ->sortable(),
                    TextColumn::make('method')
                        ->label('Metode Presensi')
                        ->formatStateUsing(fn ($state) => $state == 'network' ? 'Jaringan' : 'Lokasi')
                        ->sortable(),
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

                            $query->whereMonth('presence_date', $date->month)
                                ->whereYear('presence_date', $date->year);
                        })
                        ->indicateUsing(function (array $data) {
                            if (empty($data['value'])) return [];

                            return [
                                'Bulan: ' . Carbon::parse($data['value'])->translatedFormat('F Y'),
                            ];
                        })
                        ->selectablePlaceholder(false)
                        ->native(false)
                ])
                ->hiddenFilterIndicators();
        } else {
            $unit = $this->tableFilters['unit']['value'] ?? 0;
            $unit = $unit == 0 ? 1 : $unit;
            
            return $table
                ->recordTitleAttribute('name')
                ->query(Staff::query())
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
                        ->default(fn() => Unit::first()?->id) // Default ke ID unit pertama
                        ->selectablePlaceholder(false) 
                        ->native(false)
                        ->searchable()
                        ->preload(),
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
                        ->query(function (Builder $query) {
                            return $query;
                        })
                        ->indicateUsing(function (array $data) {
                            if (empty($data['value'])) return [];
                            return ['Periode: ' . Carbon::createFromFormat('Y-m', $data['value'])->translatedFormat('F Y')];
                        })
                        ->selectablePlaceholder(false)
                        ->native(false)
                ])
                ->recordActions([
                    Action::make('exportPdf')
                        ->label('Export PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('warning')
                        ->visible(fn ($livewire) => $livewire->tableFilters['month_year']['value'])
                        ->modalHeading('Preview Cuti')
                        ->modalWidth('5xl')
                        ->modalContent(function ($record, $livewire) {
                            $month = $livewire->tableFilters['month_year']['value'] ?? now()->format('Y-m');

                            $data = Presence::query()
                                ->with(['staff.chair', 'staff.unit'])
                                ->where('staff_id', $record->id)
                                ->whereMonth('presence_date', substr($month, 5, 2))
                                ->whereYear('presence_date', substr($month, 0, 4))
                                ->orderBy('presence_date')
                                ->get();

                            $role = Auth::user()->role_id;

                            $html = view('exports.presences', compact('data', 'month', 'role'))->render();

                            $mpdf = new Mpdf([
                                'mode' => 'utf-8',
                                'format' => 'A4',
                                'margin_left'   => 25, // 2.5 cm
                                'margin_right'  => 20, // 2 cm
                                'margin_top'    => 25, // 2.5 cm
                                'margin_bottom' => 20, // 2 cm
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
                        ->closeModalByEscaping(false),
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
