<?php

namespace App\Filament\Resources\Duties\Pages;

use App\Filament\Resources\Duties\DutyResource;
use App\Models\Presence;
use App\Models\Schedule;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class OutlineDuty extends Page implements HasForms
{
    use InteractsWithRecord;
    use InteractsWithForms;

    protected static string $resource = DutyResource::class;

    protected string $view = 'filament.resources.duties.pages.outline-duty';

    protected static ?string $title = 'Notulensi Tugas';

    public ?array $data = [];

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $receiver = $this->record->receivers()->where('staff_id', Auth::user()->staff_id)->first();

        $this->form->fill([
            'outline'          => $receiver?->outline,
            'image_path'         => $receiver?->image_path,
            'content_path'       => $receiver?->content_path,
            'letter_path'        => $receiver?->letter_path,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Section::make('Detail Surat Tugas')
                            ->icon('heroicon-o-information-circle')
                            ->iconColor('warning')
                            ->extraAttributes([
                                'class' => '[&_.fi-sc-has-gap]:!gap-3 ' . implode(' ', [
                                    '[&_.fi-section-header]:bg-gradient-to-br',
                                    '[&_.fi-section-header]:from-emerald-500',
                                    '[&_.fi-section-header]:to-teal-600',
                                    '[&_.fi-section-header]:dark:from-emerald-900',
                                    '[&_.fi-section-header]:dark:to-teal-950',
                                    '[&_.fi-section-header]:rounded-t-2xl',
                                    '[&_.fi-section-header-heading]:!text-white',
                                    '[&_.fi-section-header-description]:!text-white/80',
                                    '[&_.fi-section-header_.fi-icon-btn]:!text-white',
                                ]),
                            ])
                            ->schema([
                                Grid::make(1)->schema([
                                    TextEntry::make('duty')
                                        ->label('Acara')
                                        ->state($this->record->duty)
                                        ->hiddenLabel()
                                        ->size(TextSize::Large)
                                        ->weight(FontWeight::Bold)
                                        ->extraAttributes([
                                            'class' => 'leading-tight'
                                        ]),

                                    TextEntry::make('date')
                                        ->label(new HtmlString('<span class="font-light text-xs">Hari, Tanggal</span>'))
                                        ->state(Carbon::parse($this->record->start_date)->translatedFormat('l, d F Y'))
                                        ->weight(FontWeight::SemiBold)
                                        ->icon('heroicon-m-calendar-days')
                                        ->iconColor('primary')
                                        ->extraAttributes([
                                            'class' => '-mt-1'
                                        ]),

                                    TextEntry::make('time')
                                        ->label(new HtmlString('<span class="font-light text-xs">Waktu</span>'))
                                        ->state(
                                            Carbon::parse($this->record->start_time)->format('H:i') . 
                                            ($this->record->end_time ? ' - ' . Carbon::parse($this->record->end_time)->format('H:i') . ' WIB' : ' WIB - Selesai')
                                        )
                                        ->weight(FontWeight::SemiBold)
                                        ->icon('heroicon-m-clock')
                                        ->iconColor('primary')
                                        ->extraAttributes([
                                            'class' => '-mt-1'
                                        ]),

                                    TextEntry::make('location')
                                        ->label(new HtmlString('<span class="font-light text-xs">Tempat</span>'))
                                        ->state($this->record->location)
                                        ->weight(FontWeight::SemiBold)
                                        ->icon('heroicon-m-map-pin')
                                        ->iconColor('primary')
                                        ->extraAttributes([
                                            'class' => '-mt-1'
                                        ]),
                                ]),
                            ])
                            ->columnSpan(1),

                        Section::make('Laporan Notulensi')
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
                                Textarea::make('outline')
                                    ->label('Catatan / Notulensi')
                                    ->required()
                                    ->rows(7),
                            ])
                            ->columnSpan(2),
                    ]),
                Section::make('Berkas Pendukung')
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
                        Grid::make(3)
                            ->schema([
                                FileUpload::make('image_path')
                                    ->label('Foto Selfie di Lokasi')
                                    ->image()
                                    ->directory('notulensi/image')
                                    ->required(),
                                    
                                FileUpload::make('content_path')
                                    ->label('Materi (PDF / PPT)')
                                    ->acceptedFileTypes([
                                        'application/pdf', 
                                        'application/vnd.ms-powerpoint', 
                                        'application/vnd.openxmlformats-officedocument.presentationml.presentation'
                                    ])
                                    ->directory('notulensi/materi'),
                                    
                                FileUpload::make('letter_path')
                                    ->label('Surat Tugas Berstempel')
                                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                    ->directory('notulensi/surat')
                                    ->required(),
                            ])
                    ])
            ])
            ->statePath('data');
    }

    public function submitAction(): Action
    {
        return Action::make('submitAction')
            ->label('Simpan Laporan Notulensi')
            ->color('primary')
            ->modalHeading('Konfirmasi Waktu Pelaksanaan')
            ->modalDescription('Lengkapi data berikut sebelum menyimpan laporan.')
            ->modalAlignment(Alignment::Center)
            ->modalWidth('md')
            ->modalSubmitActionLabel('Simpan')
            ->requiresConfirmation()
            ->fillForm(function () {
                $pivot = $this->record->receivers()->where('staff_id', Auth::user()->staff_id)->first();

                return [
                    'is_workhour' => $pivot?->is_workhour,
                ];
            })
            ->schema([
                Grid::make(1)->schema([
                    ToggleButtons::make('is_workhour')
                        ->label(new HtmlString('<span class="text-center">Apakah kegiatan dilaksanakan ketika jam kerja?</span>'))
                        ->options([
                            1 => 'Ya',
                            0 => 'Tidak',
                        ])
                        ->colors([
                            1 => 'success',
                            0 => 'danger',
                        ])
                        ->inline()
                        ->required()
                        ->extraAttributes([
                            'class' => 'justify-center w-full',
                        ]),
                ])
                ->extraAttributes([
                    'class' => '[&_.fi-fo-field-label-ctn]:w-full [&_.fi-fo-field-label-ctn]:justify-center',
                ]),
            ])
            
            ->action(function (array $data, $record) {
                $staff = Auth::user()->staff;
                $mainData = $this->form->getState();

                $schedule = Schedule::where('staff_id', $staff->id)
                    ->whereDate('schedule_date', $record->duty_date)
                    ->with(['shift' => function($q) {
                        $q->select(['id', 'start_time', 'end_time']); 
                    }])
                    ->first();

                if (!$schedule) {
                    Notification::make()
                        ->danger()
                        ->title('Gagal Menyimpan Laporan Notulensi')
                        ->body('Anda masih belum memiliki jadwal kerja di tanggal tugas tersebut')
                        ->send();
                    return;
                }

                $updateData = [
                    'outline'      => $mainData['outline'],
                    'image_path'   => $mainData['image_path'],
                    'image_verified'   => null,
                    'content_path' => $mainData['content_path'],
                    'content_verified' => null,
                    'letter_path'  => $mainData['letter_path'],
                    'letter_verified'  => null,
                    'is_workhour'  => $data['is_workhour'],
                ];
                
                $presence_data = [
                    'staff_id' => $staff->id,
                    'presence_date' => $record->duty_date,
                    'check_in' => $schedule->shift->start_time,
                    'check_out' => $schedule->shift->end_time,
                    'method' => 'network',
                ];
                
                $this->record->receivers()->where('staff_id', $staff->id)->update($updateData);

                Presence::create($presence_data);

                Notification::make()
                    ->success()
                    ->title('Notulensi, Presensi, dan Konfirmasi Berhasil Disimpan')
                    ->send();

                $this->redirect(DutyResource::getUrl('index'));
            });
    }
}