<?php

namespace App\Filament\Resources\StaffAdministrations\Schemas;

use App\Models\Staff;
use Carbon\Carbon;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class StaffAdministrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('staff.name')
                    ->relationship('staff', 'name')
                    ->label('Nama Pegawai')
                    ->inlineLabel()
                    ->searchable()
                    ->hidden()
                    ->dehydrated()
                    ->required()
                    ->columnSpanFull(),

                Section::make('Kelengkapan Dokumen')
                    ->description('Unggah berkas PDF (Maks. 2MB) dan atur masa berlakunya.')
                    ->icon('heroicon-m-folder-open')
                    ->collapsible()
                    ->extraAttributes(fn ($record) => [
                        'class' => implode(' ', [
                            '[&>section>.fi-section-header]:bg-gradient-to-br',
                            '[&>section>.fi-section-header]:rounded-t-2xl',
                            '[&>section>.fi-section-header_.fi-section-header-heading]:!text-white',
                            '[&>section>.fi-section-header_.fi-section-header-description]:!text-white/80',
                            '[&>section>.fi-section-header_.fi-icon-btn]:!text-white',

                            ($record->staff->group_id >= 9 || $record->staff->group_id == 1) 
                                ? '[&>section>.fi-section-header]:from-emerald-500 [&>section>.fi-section-header]:to-teal-600 [&>section>.fi-section-header]:dark:from-emerald-900 [&>section>.fi-section-header]:dark:to-teal-950'
                                : '[&>section>.fi-section-header]:from-blue-400 [&>section>.fi-section-header]:to-sky-900 [&>section>.fi-section-header]:dark:from-blue-500 [&>section>.fi-section-header]:dark:to-sky-950',
                        ])
                    ])
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 1,
                            'md' => 2,
                            'xl' => 3,
                        ])
                        ->gap(4)
                        ->schema([
                            self::makeDocumentEntry(
                                name: 'sip',
                                label: 'Surat Izin Praktek (SIP)',
                                expiryField: 'sip_expiry'
                            )->hidden(fn ($record) => (int) $record->staff->group_id >= 9 || $record->staff->group_id == 1),
                            self::makeDocumentEntry(
                                name: 'str', 
                                label: 'Surat Tanda Registrasi (STR)', 
                                expiryField: 'str_expiry', 
                                withLifetime: true
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
                    ->columnSpan(['lg' => 2]),
            ]);
    }

    protected static function makeDocumentEntry(string $name, string $label, string $expiryField, bool $withLifetime = false): Component
    {   
        return Section::make($label)
            ->compact()
            ->icon('heroicon-o-document-text')
            ->iconColor('primary')
            ->schema([
                FileUpload::make($name)
                    ->hiddenLabel()
                    ->disk('public')
                    ->directory($name)
                    ->visibility('public')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(2048)
                    ->downloadable()
                    ->openable()
                    ->previewable(true)
                    ->columnSpanFull(),
                
                Grid::make(['default' => 1, 'lg' => $withLifetime ? 2 : 1])
                    ->extraAttributes([
                        'class' => implode(' ', [
                            "[&_.fi-grid-col:has(input[type='checkbox'])_.fi-sc-component]:flex",
                            "[&_.fi-grid-col:has(input[type='checkbox'])_.fi-sc-component]:flex-column",
                            "[&_.fi-grid-col:has(input[type='checkbox'])_.fi-sc-component]:items-center",
                            "[&_.fi-grid-col:has(input[type='checkbox'])_.fi-sc-component]:-mt-2",
                            "[&_.fi-grid-col:has(input[type='checkbox'])_.fi-sc-component]:lg:mt-0",
                        ])
                    ])
                    ->schema([
                        DatePicker::make($expiryField)
                            ->label('Berlaku Hingga')
                            ->prefixIcon('heroicon-m-calendar-days')
                            ->native(false)
                            ->displayFormat('d F Y')
                            ->closeOnDateSelection()
                            ->disabled(fn (Get $get) => $withLifetime && $get($name . '_is_lifetime'))
                            ->dehydrated()
                            ->columnSpan(1),

                        Checkbox::make($name . '_is_lifetime')
                            ->label('Seumur Hidup')
                            ->visible($withLifetime) 
                            ->live() 
                            ->dehydrated(false) 
                            ->afterStateUpdated(function ($state, Set $set, Get $get) use ($name) {
                                if ($state) {
                                    $staffId = $get('staff_id') ?? $get('staff'); 

                                    if ($staffId) {
                                        $staff = Staff::find($staffId);
                                        if ($staff && $staff->birth_date) {
                                            $lifetimeDate = Carbon::parse($staff->birth_date)->addYears(60);
                                            $set('str_expiry', $lifetimeDate->format('Y-m-d'));
                                        } else {
                                            Notification::make()->title('Data Tanggal Lahir Staff kosong!')->danger()->send();
                                            $set($name . '_is_lifetime', false);
                                        }
                                    }
                                }
                            })
                            ->columnSpan(1),
                    ]),
            ])
            ->collapsible();
    }
}
