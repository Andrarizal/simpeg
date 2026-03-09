<?php

namespace App\Filament\Resources\Letters\Pages;

use App\Filament\Resources\Letters\LetterResource;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class OutlineLetter extends Page implements HasForms
{
    use InteractsWithRecord;
    use InteractsWithForms;

    protected static string $resource = LetterResource::class;

    protected string $view = 'filament.resources.letters.pages.outline-letter';

    public static ?string $title = 'Notulensi Acara';

    public ?array $data = [];

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $receiver = $this->record->receiver()->where('staff_id', Auth::user()->staff_id)->first();

        $this->form->fill([
            'outline'       => $receiver?->outline,
            'content_path'  => $receiver?->content_path,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Section::make('Detail Acara')
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
                                    TextEntry::make('title')
                                        ->label('Acara')
                                        ->state(new HtmlString(nl2br(e(trim($this->record->title)))))
                                        ->hiddenLabel()
                                        ->size(function ($state) {
                                            if (strlen($state) > 50) {
                                                return TextSize::Medium;
                                            }
                                            return TextSize::Large;
                                        })
                                        ->weight(FontWeight::Bold)
                                        ->extraAttributes([
                                            'class' => 'leading-tight',
                                        ]),

                                    TextEntry::make('date')
                                        ->label(new HtmlString('<span class="font-light text-xs">Hari, Tanggal</span>'))
                                        ->state(fn () => Carbon::parse($this->record->start_date)->translatedFormat('l, d F Y') . ($this->record->end_date && $this->record->end_date !== $this->record->start_date ? ' - ' . Carbon::parse($this->record->end_date)->translatedFormat('l, d F Y') : ''))
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

                        Section::make('Notulensi Acara & Presensi')
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
                                    ->rows(5),
                                FileUpload::make('content_path')
                                    ->label('Materi (PDF / PPT)')
                                    ->acceptedFileTypes([
                                        'application/pdf', 
                                        'application/vnd.ms-powerpoint', 
                                        'application/vnd.openxmlformats-officedocument.presentationml.presentation'
                                    ])
                                    ->directory('notulensi/materi'),
                            ])
                            ->columnSpan(2),
                    ]),
            ])
            ->statePath('data');
    }

    public function submitAction(): Action
    {
        return Action::make('submitAction')
            ->label('Presensi')
            ->color('primary')
            ->action(function (array $data) {
                $staff = Auth::user()->staff;
                $mainData = $this->form->getState();

                $updateData = [
                    'outline'      => $mainData['outline'],
                    'content_path' => $mainData['content_path'],
                    'is_attend'    => 1,
                ];

                $this->record->receiver()->where('staff_id', $staff->id)->update($updateData);

                Notification::make()
                    ->success()
                    ->title('Notulensi dan Presensi Berhasil Disimpan')
                    ->send();

                $this->redirect(LetterResource::getUrl('index'));
            });
    }
}
