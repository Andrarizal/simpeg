<?php

namespace App\Filament\Resources\StaffAdministrations\Schemas;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Illuminate\Support\HtmlString;

class StaffAdministrationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('staff.pas')
                            ->hiddenLabel()
                            ->html()
                            ->extraAttributes(['class' => 'flex justify-center'])
                            ->state(function ($record) {
                                $imageUrl = null;

                                if ($record->staff->pas && asset('storage/' . $record->staff->pas)) {
                                    $imageUrl = asset('storage/' . $record->staff->pas);
                                }

                                $initials = collect(explode(' ', $record->staff->name))
                                    ->map(fn ($segment) => $segment[0] ?? '')
                                    ->take(2)
                                    ->join('');

                                if ($imageUrl) {
                                    return '
                                        <div class="shrink-0 relative">
                                            <img src="' . $imageUrl . '" 
                                                alt="' . $record->staff->name . '" 
                                                class="w-28 h-28 rounded-full object-cover border-4 border-white/20 shadow-md bg-gray-200">
                                        </div>
                                    ';
                                }

                                return '
                                    <div class="shrink-0 relative">
                                        <div class="w-28 h-28 rounded-full bg-gray-500 flex items-center justify-center text-3xl font-bold text-white border-4 border-white/10 shadow-md">
                                            ' . strtoupper($initials) . '
                                        </div>
                                    </div>
                                ';
                            }),
                        TextEntry::make('staff.name')
                            ->hiddenLabel()
                            ->alignCenter()
                            ->extraAttributes([
                                'class' => 'text-center font-bold text-xl -mt-2',
                            ]),
                        TextEntry::make('staff.nip')
                            ->hiddenLabel()
                            ->alignCenter()
                            ->extraAttributes([
                                'class' => 'text-center text-sm text-gray-500 -mt-6',
                            ]),
                        TextEntry::make('is_verified')
                            ->label('Status Verifikasi')
                            ->badge()
                            ->extraAttributes([
                                'class' => '-mt-1'
                            ])
                            ->default('-')
                            ->formatStateUsing(fn ($state) => match ($state) {
                                1, true, '1' => 'Terverifikasi',
                                0, false, '0' => 'Tidak Terverifikasi',
                                default => 'Belum Terverifikasi',
                            })
                            ->color(fn ($state) => match ($state) {
                                1, true, '1' => 'success',
                                0, false, '0' => 'danger',
                                default => 'warning',
                            })
                            ->icon(fn ($state) => match ($state) {
                                1, true, '1' => 'heroicon-m-check-badge',
                                0, true, '0' => 'heroicon-m-no-symbol',
                                default => 'heroicon-m-clock',
                            }),
                        TextEntry::make('note')
                            ->label('Catatan Verifikasi')
                            ->default('Tidak ada')
                            ->columnSpanFull()
                            ->extraAttributes([
                                'class' => '-mt-1'
                            ])
                            ->color(fn ($record) => $record->note ? 'danger' : 'gray')
                    ])
                    ->columns(1),

                Section::make('Kelengkapan Dokumen')
                    ->collapsible()
                    ->description(fn ($record) => new HtmlString(
                        '<span class="inline-flex items-center rounded-xl bg-white/20 px-2.5 py-0.5 text-xs font-medium text-white ring-1 ring-inset ring-white/30">' . 
                            ($record->staff->group_id >= 9 || $record->staff->group_id == 1 ? 'Non-Nakes' : 'Nakes') . 
                        '</span>'
                    ))
                    ->extraAttributes(fn ($record) => [
                        'class' => implode(' ', [
                            '[&_.fi-section-header]:bg-gradient-to-br',
                            '[&_.fi-section-header]:rounded-t-2xl',
                            '[&_.fi-section-header-heading]:!text-white',
                            '[&_.fi-section-header-description]:!text-white/80',
                            '[&_.fi-section-header_.fi-icon-btn]:!text-white',

                            ($record->staff->group_id >= 9 || $record->staff->group_id == 1) 
                                ? '[&_.fi-section-header]:from-emerald-500 [&_.fi-section-header]:to-teal-600 [&_.fi-section-header]:dark:from-emerald-900 [&_.fi-section-header]:dark:to-teal-950'
                                : '[&_.fi-section-header]:from-blue-400 [&_.fi-section-header]:to-sky-900 [&_.fi-section-header]:dark:from-blue-500 [&_.fi-section-header]:dark:to-sky-950',
                        ])
                    ])
                    ->schema([
                        Grid::make([
                                'default' => 1,
                                'md' => 2,
                                'xl' => 3,
                            ])
                            ->schema([
                                self::makeDocumentEntry(
                                    name: 'sip',
                                    label: 'Surat Izin Praktek (SIP)',
                                    expiryField: 'sip_expiry'
                                )->hidden(fn ($record) => (int) $record->staff->group_id >= 9 || $record->staff->group_id == 1),
                                self::makeDocumentEntry(
                                    name: 'str',
                                    label: 'Surat Tanda Registrasi (STR)',
                                    expiryField: 'str_expiry'
                                )->hidden(fn ($record) => (int) $record->staff->group_id >= 9 || $record->staff->group_id == 1),
                                self::makeDocumentEntry(
                                    name: 'spk',
                                    label: 'Surat Penugasan Klinis (SPK)',
                                    expiryField: 'spk_expiry'
                                )->hidden(fn ($record) => (int) $record->staff->group_id >= 9 || $record->staff->group_id == 1),
                                self::makeDocumentEntry(
                                    name: 'rkk',
                                    label: 'Rencana Kewenangan Klinis (RKK)',
                                    expiryField: 'rkk_expiry'
                                )->hidden(fn ($record) => (int) $record->staff->group_id >= 9 || $record->staff->group_id == 1),
                                self::makeDocumentEntry(
                                    name: 'mcu',
                                    label: 'Medical Check Up (MCU)',
                                    expiryField: 'mcu_expiry'
                                ),
                                self::makeDocumentEntry(
                                    name: 'utw',
                                    label: 'Uraian Tugas & Wewenang (UTW)',
                                    expiryField: 'utw_expiry'
                                ),
                            ]),
                    ])
                    ->columnSpan(2),
            ]);
    }

    private static function makeDocumentEntry(string $name, string $label, string $expiryField): TextEntry
    {
        return TextEntry::make($name)
            ->label($label)
            ->placeholder('Belum diunggah')
            ->icon(fn ($state) => $state ? 'heroicon-m-document-text' : 'heroicon-m-x-circle')
            ->iconColor(fn ($state) => $state ? 'primary' : 'gray')
            ->formatStateUsing(fn ($state) => $state ? substr(basename($state), 0, 15) . "..." : null)
            ->helperText(function ($record) use ($expiryField) {
                if (! $record->{$expiryField}) return null;
                $date = Carbon::parse($record->{$expiryField});
                $now = now();

                $isExpired = $date->isPast();
                $isAlmostExpired = !$isExpired && $date->lte($now->copy()->addMonths(6));

                $dateString = $date->format('d M Y');

                if ($isExpired) {
                    $text = 'Berlaku s.d: ' . $dateString;
                    return new HtmlString("<span class='text-danger-600 font-bold'>{$text}</span>");
                }

                if ($isAlmostExpired) {
                    $text = 'Berlaku s.d: ' . $dateString;
                    return new HtmlString("<span class='text-warning-600 font-bold'>{$text}</span>");
                }

                return 'Berlaku s.d: ' . $dateString;
            })

            ->color(function ($record) use ($expiryField) {
                if (! $record->{$expiryField}) return 'gray';
                $date = Carbon::parse($record->{$expiryField});

                if ($date->isPast()) {
                    return 'danger';
                }
                if ($date->lte(now()->addMonths(6))) {
                    return 'warning';
                }
                return 'gray'; 
            })
            
            ->suffixAction(
                Action::make('show_' . $name)
                    ->icon('heroicon-m-eye')
                    ->tooltip('Lihat Dokumen')
                    ->iconButton()
                    ->color('primary')
                    ->visible(fn ($record) => $record->{$name} != null)
                    ->modalWidth('5xl')
                    ->modalHeading(fn ($record) => "Preview {$label} - " . $record->staff->name)
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalContent(function ($record) use ($name) {
                        return view('filament.components.preview-pdf-2', [
                            'url' => route('preview.administration', [
                                'model' => 'administration',
                                'id' => $record->id,
                                'field' => $name
                            ])
                        ]);
                    })
            );
    }
}
